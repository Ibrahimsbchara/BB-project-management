<?php
function getDb(): PDO {
    $db = new PDO('sqlite:' . __DIR__ . '/sprint.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("PRAGMA journal_mode=WAL");

    $db->exec("CREATE TABLE IF NOT EXISTS tasks (
        id          INTEGER PRIMARY KEY AUTOINCREMENT,
        name        TEXT    NOT NULL,
        description TEXT    NOT NULL DEFAULT '',
        category    TEXT    NOT NULL DEFAULT 'General',
        department  TEXT    NOT NULL DEFAULT 'General',
        priority    TEXT    NOT NULL DEFAULT 'normal',
        effort      REAL    NOT NULL,
        accepted    INTEGER NOT NULL DEFAULT 0,
        source_id   TEXT
    )");

    // Migrate existing DBs
    try { $db->exec("ALTER TABLE tasks ADD COLUMN department TEXT NOT NULL DEFAULT 'General'"); } catch (\Throwable $e) {}
    try { $db->exec("ALTER TABLE tasks ADD COLUMN source_id TEXT"); } catch (\Throwable $e) {}
    try { $db->exec("ALTER TABLE tasks ADD COLUMN contribution TEXT NOT NULL DEFAULT ''"); } catch (\Throwable $e) {}

    // Rename medium → normal to match ClickUp naming
    try { $db->exec("UPDATE tasks SET priority='normal' WHERE priority='medium'"); } catch (\Throwable $e) {}

    $db->exec("CREATE TABLE IF NOT EXISTS categories (
        id   INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL UNIQUE
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS departments (
        id   INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL UNIQUE
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS sprint_settings (
        id       INTEGER PRIMARY KEY CHECK(id=1),
        capacity REAL    NOT NULL DEFAULT 70,
        locked   INTEGER NOT NULL DEFAULT 0
    )");

    $db->exec("INSERT OR IGNORE INTO sprint_settings (id, capacity, locked) VALUES (1, 70, 0)");

    $catCount = (int) $db->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    if ($catCount === 0) {
        $stmt = $db->prepare("INSERT OR IGNORE INTO categories (name) VALUES (?)");
        foreach (['Frontend', 'Backend', 'Design', 'QA'] as $cat) {
            $stmt->execute([$cat]);
        }
    }

    $deptCount = (int) $db->query("SELECT COUNT(*) FROM departments")->fetchColumn();
    if ($deptCount === 0) {
        $stmt = $db->prepare("INSERT OR IGNORE INTO departments (name) VALUES (?)");
        foreach (['Operations', 'CS', 'Product', 'Tech', 'Design', 'Marketing'] as $dept) {
            $stmt->execute([$dept]);
        }
    }

    return $db;
}
