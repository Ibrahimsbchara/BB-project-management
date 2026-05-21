<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

$action = $_GET['action'] ?? '';
$input  = (array) json_decode(file_get_contents('php://input'), true);

function jsonOut(mixed $data): void {
    echo json_encode($data);
    exit;
}

// READ-ONLY: this function only makes HTTP GET requests to ClickUp.
// It never creates, modifies, or deletes anything in ClickUp.
function clickupGet(string $path): array {
    $apiKey = 'pk_101409530_POXTQVOA1A8RU86N4LBU2Z62ACEMIXI0';
    $url    = 'https://api.clickup.com/api/v2' . $path;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ["Authorization: $apiKey", "Content-Type: application/json"],
        CURLOPT_CUSTOMREQUEST  => 'GET',   // enforce GET — never POST/PUT/DELETE
        CURLOPT_TIMEOUT        => 25,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $body   = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err    = curl_error($ch);
    curl_close($ch);
    if ($err)            throw new RuntimeException("cURL error: $err");
    if ($status !== 200) throw new RuntimeException("ClickUp API returned $status: $body");
    $data = json_decode($body, true);
    if (!is_array($data)) throw new RuntimeException("Invalid JSON from ClickUp");
    return $data;
}

try {
    $db = getDb();

    switch ($action) {

        /* ---- READ ---- */
        case 'init':
            $tasks       = $db->query("SELECT * FROM tasks ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
            $categories  = $db->query("SELECT name FROM categories ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);
            $departments = $db->query("SELECT name FROM departments ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);
            $settings    = $db->query("SELECT * FROM sprint_settings WHERE id=1")->fetch(PDO::FETCH_ASSOC);
            jsonOut([
                'tasks'       => $tasks,
                'categories'  => $categories,
                'departments' => $departments,
                'capacity'    => (float) $settings['capacity'],
                'locked'      => (bool)  $settings['locked'],
            ]);

        /* ---- TASKS ---- */
        case 'create_task':
            $stmt = $db->prepare("INSERT INTO tasks (name, description, category, department, priority, effort)
                                  VALUES (:name, :desc, :cat, :dept, :pri, :eff)");
            $stmt->execute([
                ':name' => $input['name'],
                ':desc' => $input['desc']       ?? '',
                ':cat'  => $input['tag']         ?? 'General',
                ':dept' => $input['department']  ?? 'General',
                ':pri'  => $input['priority']    ?? 'normal',
                ':eff'  => (float) ($input['effort'] ?? 0),
            ]);
            $id   = (int) $db->lastInsertId();
            $task = $db->query("SELECT * FROM tasks WHERE id=$id")->fetch(PDO::FETCH_ASSOC);
            jsonOut(['success' => true, 'task' => $task]);

        case 'update_task':
            $id   = (int) ($input['id'] ?? 0);
            $stmt = $db->prepare("UPDATE tasks
                                  SET name=:name, description=:desc, category=:cat,
                                      department=:dept, priority=:pri, effort=:eff
                                  WHERE id=:id");
            $stmt->execute([
                ':name' => $input['name'],
                ':desc' => $input['desc']       ?? '',
                ':cat'  => $input['tag']         ?? 'General',
                ':dept' => $input['department']  ?? 'General',
                ':pri'  => $input['priority']    ?? 'normal',
                ':eff'  => (float) ($input['effort'] ?? 0),
                ':id'   => $id,
            ]);
            $task = $db->query("SELECT * FROM tasks WHERE id=$id")->fetch(PDO::FETCH_ASSOC);
            jsonOut(['success' => true, 'task' => $task]);

        case 'update_contribution':
            $id   = (int) ($input['id'] ?? 0);
            $stmt = $db->prepare("UPDATE tasks SET contribution=? WHERE id=?");
            $stmt->execute([$input['contribution'] ?? '', $id]);
            jsonOut(['success' => true]);

        case 'delete_task':
            $id   = (int) ($input['id'] ?? 0);
            $stmt = $db->prepare("DELETE FROM tasks WHERE id=?");
            $stmt->execute([$id]);
            jsonOut(['success' => true]);

        case 'toggle_accept':
            $id   = (int) ($input['id'] ?? 0);
            $task = $db->query("SELECT * FROM tasks WHERE id=$id")->fetch(PDO::FETCH_ASSOC);
            if (!$task) { jsonOut(['success' => false, 'error' => 'Task not found.']); }

            if (!$task['accepted']) {
                $settings = $db->query("SELECT capacity FROM sprint_settings WHERE id=1")->fetch(PDO::FETCH_ASSOC);
                $used     = (float) $db->query("SELECT COALESCE(SUM(effort),0) FROM tasks WHERE accepted=1 AND id!=$id")->fetchColumn();
                if ($used + (float) $task['effort'] > (float) $settings['capacity']) {
                    jsonOut(['success' => false, 'error' => 'Not enough capacity.']);
                }
            }

            $newVal = $task['accepted'] ? 0 : 1;
            $db->prepare("UPDATE tasks SET accepted=? WHERE id=?")->execute([$newVal, $id]);
            jsonOut(['success' => true, 'accepted' => (bool) $newVal]);

        /* ---- SPRINT ---- */
        case 'lock_sprint':
            $db->exec("UPDATE sprint_settings SET locked=1 WHERE id=1");
            jsonOut(['success' => true]);

        case 'unlock_sprint':
            $db->exec("UPDATE sprint_settings SET locked=0 WHERE id=1");
            jsonOut(['success' => true]);

        case 'clear_sprint':
            // Deletes all tasks from our local DB only — never touches ClickUp
            $db->exec("DELETE FROM tasks");
            $db->exec("UPDATE sprint_settings SET locked=0 WHERE id=1");
            jsonOut(['success' => true]);

        case 'update_capacity':
            $val  = (float) ($input['capacity'] ?? 0);
            $stmt = $db->prepare("UPDATE sprint_settings SET capacity=? WHERE id=1");
            $stmt->execute([$val]);
            jsonOut(['success' => true]);

        /* ---- CATEGORIES ---- */
        case 'add_category':
            $stmt = $db->prepare("INSERT OR IGNORE INTO categories (name) VALUES (?)");
            $stmt->execute([$input['name'] ?? '']);
            $categories = $db->query("SELECT name FROM categories ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);
            jsonOut(['success' => true, 'categories' => $categories]);

        case 'remove_category':
            $stmt = $db->prepare("DELETE FROM categories WHERE name=?");
            $stmt->execute([$input['name'] ?? '']);
            $categories = $db->query("SELECT name FROM categories ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);
            jsonOut(['success' => true, 'categories' => $categories]);

        /* ---- DEPARTMENTS ---- */
        case 'add_department':
            $stmt = $db->prepare("INSERT OR IGNORE INTO departments (name) VALUES (?)");
            $stmt->execute([$input['name'] ?? '']);
            $departments = $db->query("SELECT name FROM departments ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);
            jsonOut(['success' => true, 'departments' => $departments]);

        case 'remove_department':
            $stmt = $db->prepare("DELETE FROM departments WHERE name=?");
            $stmt->execute([$input['name'] ?? '']);
            $departments = $db->query("SELECT name FROM departments ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);
            jsonOut(['success' => true, 'departments' => $departments]);

        /* ---- CLICKUP IMPORT ---- */
        case 'clickup_fetch':
            // 1. Get workspaces — find BloomingBox
            $teams = clickupGet('/team')['teams'] ?? [];
            $team  = null;
            foreach ($teams as $t) {
                if (stripos($t['name'], 'bloomingbox') !== false || stripos($t['name'], 'blooming') !== false) {
                    $team = $t; break;
                }
            }
            if (!$team) $team = $teams[0] ?? null;
            if (!$team) jsonOut(['success' => false, 'error' => 'No workspace found in ClickUp.']);

            // 2. Get spaces — find BloomingBox Tech
            $spaces = clickupGet("/team/{$team['id']}/space?archived=false")['spaces'] ?? [];
            $space  = null;
            foreach ($spaces as $s) {
                if (stripos($s['name'], 'tech') !== false) { $space = $s; break; }
            }
            if (!$space) $space = $spaces[0] ?? null;
            if (!$space) jsonOut(['success' => false, 'error' => 'BloomingBox Tech space not found.']);

            // 3. Get folders — find Backlog
            $folders = clickupGet("/space/{$space['id']}/folder?archived=false")['folders'] ?? [];
            $folder  = null;
            foreach ($folders as $f) {
                if (stripos($f['name'], 'backlog') !== false) { $folder = $f; break; }
            }
            if (!$folder) jsonOut(['success' => false, 'error' => 'Backlog folder not found in space "' . $space['name'] . '".']);

            // 4. Get lists — find Backlog list
            $lists = clickupGet("/folder/{$folder['id']}/list?archived=false")['lists'] ?? [];
            $list  = null;
            foreach ($lists as $l) {
                if (stripos($l['name'], 'backlog') !== false) { $list = $l; break; }
            }
            if (!$list) $list = $lists[0] ?? null;
            if (!$list) jsonOut(['success' => false, 'error' => 'No lists found in Backlog folder.']);

            // 5a. Fetch READY FOR SPRINT parent tasks (no subtasks filter — status filter
            //     would exclude subtasks that have a different status anyway).
            $allTasks = [];
            $page = 0;
            do {
                $encoded  = urlencode('ready for sprint');
                $data     = clickupGet("/list/{$list['id']}/task?statuses[]={$encoded}&page={$page}&include_closed=false");
                $batch    = $data['tasks'] ?? [];
                $allTasks = array_merge($allTasks, $batch);
                $lastPage = $data['last_page'] ?? true;
                $page++;
            } while (!$lastPage && count($batch) > 0 && $page < 20);

            // 5b. Fetch ALL tasks with subtasks=true and NO status filter so we capture
            //     subtask rows regardless of their status, then sum their estimates per parent.
            //     (This is why only 1/88 had the right time before — the status filter was
            //     excluding subtasks that don't share the parent's status.)
            $subtaskEstByParent = [];
            $page = 0;
            do {
                $data  = clickupGet("/list/{$list['id']}/task?subtasks=true&page={$page}&include_closed=false");
                $batch = $data['tasks'] ?? [];
                foreach ($batch as $item) {
                    $pid = $item['parent'] ?? null;
                    if ($pid !== null) {
                        $subtaskEstByParent[$pid] = ($subtaskEstByParent[$pid] ?? 0)
                                                  + (int)($item['time_estimate'] ?? 0);
                    }
                }
                $lastPage = $data['last_page'] ?? true;
                $page++;
            } while (!$lastPage && count($batch) > 0 && $page < 20);

            // Apply rolled-up subtask estimate to any parent that has no direct estimate
            foreach ($allTasks as &$task) {
                if ((int)($task['time_estimate'] ?? 0) === 0 && isset($subtaskEstByParent[$task['id']])) {
                    $task['time_estimate'] = $subtaskEstByParent[$task['id']];
                }
            }
            unset($task);

            // 6. Check which are already imported
            $existingSourceIds = $db->query("SELECT source_id FROM tasks WHERE source_id IS NOT NULL")
                                    ->fetchAll(PDO::FETCH_COLUMN);
            $existingSet = array_flip($existingSourceIds);

            $priorityMap = ['urgent' => 'urgent', 'high' => 'high', 'normal' => 'normal', 'low' => 'low'];

            $mapped = array_map(function($t) use ($priorityMap, $existingSet) {
                $effortMs  = (int)($t['time_estimate'] ?? 0);
                $effortHrs = $effortMs > 0 ? max(0.5, round($effortMs / 3600000, 1)) : null;

                $cuPriRaw = strtolower($t['priority']['priority'] ?? 'normal');
                $priority = $priorityMap[$cuPriRaw] ?? 'normal';

                $dept = 'General';
                foreach ($t['custom_fields'] ?? [] as $cf) {
                    if (stripos($cf['name'], 'department') !== false) {
                        if ($cf['type'] === 'drop_down') {
                            $val = $cf['value'] ?? null;
                            if ($val !== null) {
                                foreach ($cf['type_config']['options'] ?? [] as $opt) {
                                    // ClickUp may return value as UUID string OR as orderindex integer
                                    if ((string)$opt['id'] === (string)$val
                                        || (int)$opt['orderindex'] === (int)$val) {
                                        $dept = $opt['name'];
                                        break;
                                    }
                                }
                            }
                        } elseif (isset($cf['value']) && $cf['value'] !== null && (string)$cf['value'] !== '') {
                            $dept = (string)$cf['value'];
                        }
                        break;
                    }
                }

                return [
                    'cu_id'            => $t['id'],
                    'name'             => $t['name'],
                    'description'      => trim(strip_tags($t['description'] ?? '')),
                    'priority'         => $priority,
                    'effort'           => $effortHrs,
                    'department'       => $dept,
                    'already_imported' => isset($existingSet[$t['id']]),
                ];
            }, $allTasks);

            jsonOut(['success' => true, 'tasks' => $mapped, 'list_name' => $list['name']]);

        case 'import_clickup_tasks':
            $incoming = $input['tasks'] ?? [];
            if (!is_array($incoming) || !count($incoming)) {
                jsonOut(['success' => false, 'error' => 'No tasks provided.']);
            }

            $existingIds = $db->query("SELECT source_id FROM tasks WHERE source_id IS NOT NULL")
                              ->fetchAll(PDO::FETCH_COLUMN);
            $existingSet = array_flip($existingIds);

            $deptStmt = $db->prepare("INSERT OR IGNORE INTO departments (name) VALUES (?)");
            $stmt = $db->prepare("INSERT INTO tasks (name, description, category, department, priority, effort, source_id)
                                  VALUES (:name, :desc, :cat, :dept, :pri, :eff, :sid)");
            $imported = 0;
            foreach ($incoming as $t) {
                $sid = $t['cu_id'] ?? null;
                if ($sid && isset($existingSet[$sid])) continue;
                $deptName = $t['department'] ?? 'General';
                if ($deptName && $deptName !== 'General') $deptStmt->execute([$deptName]);
                $stmt->execute([
                    ':name' => $t['name']        ?? 'Untitled',
                    ':desc' => $t['description'] ?? '',
                    ':cat'  => $t['tag']         ?? 'General',
                    ':dept' => $deptName,
                    ':pri'  => $t['priority']    ?? 'normal',
                    ':eff'  => (float) ($t['effort'] ?? 1),
                    ':sid'  => $sid,
                ]);
                $imported++;
            }

            $tasks = $db->query("SELECT * FROM tasks ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
            jsonOut(['success' => true, 'imported' => $imported, 'tasks' => $tasks]);

        default:
            http_response_code(400);
            jsonOut(['error' => 'Unknown action']);
    }

} catch (Throwable $e) {
    http_response_code(500);
    jsonOut(['error' => $e->getMessage()]);
}
