<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sprint Planner</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; min-height: 100vh; }

  /* NAV */
  nav {
    background: #fff; border-bottom: 1px solid #e5e7eb;
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 28px; position: sticky; top: 0; z-index: 100;
  }
  .nav-brand { font-size: 13px; font-weight: 700; color: #111827; letter-spacing: 0.04em; text-transform: uppercase; }
  .nav-tabs { display: flex; gap: 4px; }
  .nav-tab {
    padding: 7px 18px; border-radius: 8px; font-size: 13px; font-weight: 600;
    cursor: pointer; color: #6b7280; border: 1px solid transparent; background: none;
    transition: all 0.15s; font-family: 'Segoe UI', sans-serif;
  }
  .nav-tab:hover { background: #f3f4f6; color: #374151; }
  .nav-tab.active { background: #111827; color: #fff; }

  /* VIEWS */
  .view { display: none; max-width: 1300px; margin: 0 auto; padding: 32px 24px; }
  .view.active { display: block; }

  .page-heading { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 22px; gap: 16px; }
  .page-heading-text h1 { font-size: 20px; font-weight: 700; color: #111827; }
  .page-heading-text p { font-size: 14px; color: #6b7280; margin-top: 4px; }
  .page-heading-actions { display: flex; gap: 8px; align-items: center; flex-shrink: 0; }

  /* BUTTONS */
  .btn-primary {
    background: #111827; color: #fff; border: none; border-radius: 8px;
    padding: 10px 20px; font-size: 13px; font-weight: 600; cursor: pointer;
    font-family: 'Segoe UI', sans-serif; transition: opacity 0.15s; white-space: nowrap;
    display: inline-flex; align-items: center; gap: 7px;
  }
  .btn-primary:hover { opacity: 0.85; }
  .btn-primary:disabled { opacity: 0.42; cursor: not-allowed; }
  .btn-ghost {
    background: none; color: #6b7280; border: 1.5px solid #e5e7eb; border-radius: 8px;
    padding: 9px 18px; font-size: 13px; font-weight: 600; cursor: pointer;
    font-family: 'Segoe UI', sans-serif; transition: all 0.15s; white-space: nowrap;
    display: inline-flex; align-items: center; gap: 7px;
  }
  .btn-ghost:hover { border-color: #d1d5db; color: #374151; }
  .btn-danger {
    background: #fff; color: #ef4444; border: 1.5px solid #fca5a5; border-radius: 8px;
    padding: 9px 18px; font-size: 13px; font-weight: 600; cursor: pointer;
    font-family: 'Segoe UI', sans-serif; transition: all 0.15s;
  }
  .btn-danger:hover { background: #fef2f2; }

  /* TABLE */
  .table-wrap { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; overflow-x: auto; }
  table { width: 100%; border-collapse: collapse; min-width: 900px; }
  thead tr { background: #f9fafb; border-bottom: 1px solid #e5e7eb; }
  thead th { padding: 11px 16px; font-size: 11px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.06em; text-align: left; white-space: nowrap; }
  tbody tr { border-bottom: 1px solid #f3f4f6; transition: background 0.12s; }
  tbody tr:last-child { border-bottom: none; }
  tbody tr:hover { background: #fafafa; }
  tbody td { padding: 13px 16px; font-size: 14px; color: #374151; vertical-align: middle; }
  .td-name { font-weight: 600; color: #111827; max-width: 240px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .td-desc { color: #6b7280; font-size: 13px; max-width: 160px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .td-actions { display: flex; gap: 6px; align-items: center; white-space: nowrap; }
  .tbl-btn {
    padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;
    border: 1.5px solid #e5e7eb; background: #fff; color: #6b7280; transition: all 0.13s; font-family: 'Segoe UI', sans-serif;
  }
  .tbl-btn:hover { border-color: #d1d5db; color: #374151; }
  .tbl-btn.del:hover { border-color: #fca5a5; color: #ef4444; background: #fef2f2; }

  /* PRIORITY BADGES */
  .badge { display: inline-block; font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 99px; letter-spacing: 0.03em; }
  .badge-low    { background: #eff6ff; color: #3b82f6; }
  .badge-normal { background: #fffbeb; color: #d97706; }
  .badge-high   { background: #fef2f2; color: #ef4444; }
  .badge-urgent { background: #fff7ed; color: #ea580c; }

  /* STATUS BADGE */
  .badge-pending  { background: #fffbeb; color: #d97706; }
  .badge-accepted { background: #f0fdf4; color: #16a34a; }

  /* CHIPS */
  .cat-chip {
    display: inline-flex; align-items: center; gap: 3px; font-size: 11px; padding: 3px 8px;
    border-radius: 5px; background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb;
    cursor: pointer; transition: all 0.13s; white-space: nowrap; user-select: none;
  }
  .cat-chip:hover { background: #e5e7eb; color: #374151; border-color: #d1d5db; }
  .dept-chip {
    display: inline-flex; align-items: center; gap: 3px; font-size: 11px; padding: 3px 8px;
    border-radius: 5px; background: #ede9fe; color: #7c3aed; border: 1px solid #ddd6fe;
    cursor: pointer; transition: all 0.13s; white-space: nowrap; user-select: none;
  }
  .dept-chip:hover { background: #ddd6fe; border-color: #c4b5fd; }
  .chip-arrow { font-size: 9px; opacity: 0.55; }

  /* CHIP DROPDOWN */
  #chip-dropdown {
    display: none; position: fixed; z-index: 400;
    background: #fff; border: 1.5px solid #e5e7eb; border-radius: 10px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.13); padding: 4px;
    min-width: 160px; max-height: 220px; overflow-y: auto;
  }
  .chip-dd-item {
    padding: 9px 12px; border-radius: 6px; font-size: 13px; color: #374151;
    cursor: pointer; transition: background 0.12s;
  }
  .chip-dd-item:hover { background: #f3f4f6; }
  .chip-dd-item.dd-active { font-weight: 700; color: #111827; }
  .chip-dd-empty { padding: 10px 12px; font-size: 12px; color: #9ca3af; }

  .empty-row td { text-align: center; padding: 48px 16px; color: #d1d5db; font-size: 14px; }

  /* POPUP / MODAL SHARED */
  .backdrop {
    position: fixed; inset: 0; background: rgba(0,0,0,0.32); z-index: 200;
    display: flex; align-items: center; justify-content: center; padding: 24px;
    opacity: 0; pointer-events: none; transition: opacity 0.18s;
  }
  .backdrop.open { opacity: 1; pointer-events: all; }
  .popup {
    background: #fff; border-radius: 14px; border: 1px solid #e5e7eb;
    width: 100%; max-width: 500px; padding: 28px; position: relative;
    box-shadow: 0 8px 40px rgba(0,0,0,0.12);
    transform: translateY(14px); transition: transform 0.2s;
  }
  .backdrop.open .popup { transform: translateY(0); }
  .popup-close {
    position: absolute; top: 16px; right: 16px; background: #f3f4f6;
    border: none; color: #6b7280; width: 28px; height: 28px; border-radius: 7px;
    cursor: pointer; font-size: 16px; display: flex; align-items: center; justify-content: center; transition: all 0.15s;
  }
  .popup-close:hover { background: #e5e7eb; color: #111827; }
  .popup-title { font-size: 17px; font-weight: 700; color: #111827; margin-bottom: 20px; padding-right: 32px; }

  /* FORM */
  .form-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 14px; }
  label { font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; }
  input[type=text], input[type=number], textarea, select {
    border: 1.5px solid #e5e7eb; border-radius: 8px; padding: 9px 13px;
    font-size: 14px; color: #111827; font-family: 'Segoe UI', sans-serif;
    outline: none; transition: border 0.18s; background: #fff; width: 100%;
  }
  input:focus, textarea:focus, select:focus { border-color: #111827; }
  textarea { resize: vertical; min-height: 72px; }

  .priority-row { display: flex; gap: 8px; }
  .priority-btn {
    flex: 1; padding: 9px 4px; border-radius: 7px; border: 1.5px solid #e5e7eb;
    background: #fff; color: #9ca3af; font-size: 12px; font-weight: 600;
    cursor: pointer; text-align: center; transition: all 0.15s;
  }
  .priority-btn.sel-low    { background: #eff6ff; border-color: #93c5fd; color: #3b82f6; }
  .priority-btn.sel-normal { background: #fffbeb; border-color: #fcd34d; color: #d97706; }
  .priority-btn.sel-high   { background: #fef2f2; border-color: #fca5a5; color: #ef4444; }
  .priority-btn.sel-urgent { background: #fff7ed; border-color: #fed7aa; color: #ea580c; }

  .popup-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }

  /* DETAIL MODAL */
  .modal-sec-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #9ca3af; margin-bottom: 6px; }
  .modal-desc-text { font-size: 14px; color: #374151; line-height: 1.65; margin-bottom: 20px; }
  .modal-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 22px; }
  .meta-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px 14px; }
  .meta-key { font-size: 11px; color: #9ca3af; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
  .meta-val { font-size: 15px; font-weight: 700; color: #111827; }

  /* CONFIRM */
  .confirm-popup { max-width: 360px; }
  .confirm-msg { font-size: 14px; color: #6b7280; line-height: 1.55; margin-bottom: 22px; }

  /* DASHBOARD */
  .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 22px; margin-bottom: 22px; }
  .effort-card { background: #f9fafb; border: 1.5px solid #e5e7eb; border-radius: 12px; padding: 20px 22px; margin-bottom: 22px; }
  .effort-top { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 12px; }
  .effort-title-label { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #6b7280; }
  .effort-numbers { font-size: 22px; font-weight: 700; color: #111827; }
  .effort-numbers span { font-size: 13px; font-weight: 500; color: #9ca3af; }
  .bar-track { height: 10px; background: #e5e7eb; border-radius: 99px; overflow: hidden; }
  .bar-fill { height: 100%; border-radius: 99px; background: #111827; transition: width 0.4s ease, background 0.4s; }
  .bar-fill.warn { background: #f59e0b; }
  .bar-fill.over { background: #ef4444; }
  .bar-sub { display: flex; justify-content: space-between; margin-top: 8px; font-size: 12px; color: #9ca3af; }

  .lock-banner { display: flex; align-items: center; gap: 12px; background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; }
  .lock-text { flex: 1; font-size: 13px; font-weight: 600; color: #16a34a; }
  .lock-sub { font-size: 12px; color: #4ade80; font-weight: 400; }

  .filter-bar { display: flex; gap: 7px; margin-bottom: 16px; flex-wrap: wrap; }
  .filter-chip {
    padding: 6px 14px; border-radius: 99px; font-size: 12px; font-weight: 600;
    border: 1.5px solid #e5e7eb; color: #9ca3af; cursor: pointer; background: #fff; transition: all 0.15s;
  }
  .filter-chip:hover { color: #374151; border-color: #d1d5db; }
  .filter-chip.active { background: #111827; color: #fff; border-color: #111827; }

  .section-label { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #9ca3af; margin-bottom: 12px; }

  .task-list { display: flex; flex-direction: column; gap: 9px; }
  .task-card {
    background: #fff; border: 1.5px solid #e5e7eb; border-radius: 12px;
    padding: 13px 16px; display: flex; align-items: center; gap: 13px;
    cursor: pointer; transition: border-color 0.15s, background 0.13s; position: relative;
  }
  .task-card:hover { border-color: #d1d5db; background: #fafafa; }
  .task-card.selected { border-color: #111827; background: #f9fafb; }
  .task-card.locked-card { cursor: default; }
  .task-card.locked-card:hover { border-color: #111827; background: #f9fafb; }
  .task-card.over-budget { opacity: 0.38; pointer-events: none; }

  .tc-check { width: 20px; height: 20px; border-radius: 6px; border: 1.5px solid #d1d5db; background: #fff; flex-shrink: 0; display: flex; align-items: center; justify-content: center; transition: all 0.15s; }
  .task-card.selected .tc-check { background: #111827; border-color: #111827; }
  .tc-check svg { display: none; }
  .task-card.selected .tc-check svg { display: block; }
  .tc-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
  .tc-dot.low    { background: #93c5fd; }
  .tc-dot.normal { background: #fcd34d; }
  .tc-dot.high   { background: #fca5a5; }
  .tc-dot.urgent { background: #fdba74; }
  .tc-body { flex: 1; min-width: 0; }
  .tc-name { font-size: 14px; font-weight: 600; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .tc-meta { display: flex; gap: 6px; margin-top: 4px; flex-wrap: wrap; }
  .tc-tag  { display: inline-block; font-size: 11px; padding: 2px 8px; border-radius: 5px; background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }
  .tc-dept { display: inline-block; font-size: 11px; padding: 2px 8px; border-radius: 5px; background: #ede9fe; color: #7c3aed; border: 1px solid #ddd6fe; }
  .tc-hrs { font-size: 13px; font-weight: 600; color: #6b7280; white-space: nowrap; flex-shrink: 0; }
  .tc-info { width: 28px; height: 28px; border-radius: 7px; border: 1.5px solid #e5e7eb; background: #fff; color: #9ca3af; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.15s; }
  .tc-info:hover { border-color: #111827; color: #111827; }
  .over-label { position: absolute; right: 14px; top: 9px; font-size: 10px; font-weight: 700; color: #ef4444; text-transform: uppercase; letter-spacing: 0.06em; }
  .sprint-submit-row { margin-top: 18px; display: flex; justify-content: flex-end; }

  /* SETTINGS */
  .settings-block { margin-bottom: 8px; }
  .settings-block-title { font-size: 14px; font-weight: 700; color: #111827; margin-bottom: 4px; }
  .settings-block-desc { font-size: 13px; color: #6b7280; margin-bottom: 14px; line-height: 1.5; }
  .settings-row { display: flex; gap: 10px; align-items: center; }
  .divider { border: none; border-top: 1px solid #e5e7eb; margin: 22px 0; }

  .cat-list { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 14px; min-height: 36px; }
  .cat-item {
    display: inline-flex; align-items: center; gap: 6px; background: #f3f4f6; border: 1px solid #e5e7eb;
    border-radius: 7px; padding: 5px 10px; font-size: 13px; color: #374151; font-weight: 500;
  }
  .cat-item.dept-item { background: #ede9fe; border-color: #ddd6fe; color: #7c3aed; }
  .cat-remove { background: none; border: none; color: #9ca3af; cursor: pointer; font-size: 14px; line-height: 1; padding: 0 2px; transition: color 0.13s; }
  .cat-remove:hover { color: #ef4444; }
  .cat-add-row { display: flex; gap: 8px; }
  .cat-add-row input { flex: 1; }

  /* IMPORT MODAL */
  .import-popup { max-width: 660px; display: flex; flex-direction: column; max-height: 88vh; }
  .import-subtitle { font-size: 13px; color: #6b7280; margin-bottom: 12px; }
  .import-ctrl-bar { display: flex; gap: 8px; align-items: center; margin-bottom: 10px; }
  .import-task-list { overflow-y: auto; max-height: 400px; border: 1.5px solid #e5e7eb; border-radius: 10px; }
  .import-task-item {
    display: flex; align-items: center; gap: 10px; padding: 11px 14px;
    cursor: pointer; transition: background 0.12s; border-bottom: 1px solid #f3f4f6;
  }
  .import-task-item:last-child { border-bottom: none; }
  .import-task-item:hover { background: #f9fafb; }
  .import-task-item.sel-import { background: #f0f9ff; }
  .import-task-item.already-done { opacity: 0.42; cursor: default; pointer-events: none; }
  .import-chk {
    width: 18px; height: 18px; border-radius: 5px; border: 1.5px solid #d1d5db;
    flex-shrink: 0; display: flex; align-items: center; justify-content: center; transition: all 0.15s;
  }
  .import-task-item.sel-import .import-chk { background: #111827; border-color: #111827; }
  .import-task-name { flex: 1; font-size: 13px; font-weight: 500; color: #111827; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .import-task-right { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
  .import-effort { font-size: 12px; color: #9ca3af; white-space: nowrap; }
  .already-badge { font-size: 10px; font-weight: 700; text-transform: uppercase; color: #16a34a; background: #f0fdf4; border: 1px solid #86efac; border-radius: 4px; padding: 1px 6px; }
  .pri-badge-sm { font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 99px; white-space: nowrap; }
  .pri-badge-sm.low    { background: #eff6ff; color: #3b82f6; }
  .pri-badge-sm.normal { background: #fffbeb; color: #d97706; }
  .pri-badge-sm.high   { background: #fef2f2; color: #ef4444; }
  .pri-badge-sm.urgent { background: #fff7ed; color: #ea580c; }
  .import-footer { border-top: 1px solid #e5e7eb; padding-top: 14px; display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 14px; }
  .import-count { font-size: 13px; color: #6b7280; }
  .import-note { font-size: 12px; color: #9ca3af; margin-bottom: 10px; font-style: italic; }
  .import-loading { display: flex; flex-direction: column; align-items: center; gap: 16px; padding: 48px 0; }
  .import-loading-text { font-size: 14px; color: #9ca3af; }
  .spinner { width: 32px; height: 32px; border: 3px solid #e5e7eb; border-top-color: #111827; border-radius: 50%; animation: spin 0.75s linear infinite; }
  @keyframes spin { to { transform: rotate(360deg); } }
  .import-error-box { background: #fef2f2; border: 1.5px solid #fca5a5; border-radius: 10px; padding: 16px 18px; font-size: 14px; color: #b91c1c; line-height: 1.5; margin-bottom: 16px; }

  /* STATS CARDS */
  .stats-row { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
  .stat-card {
    background: #fff; border: 1.5px solid #e5e7eb; border-radius: 12px;
    padding: 16px 22px; flex: 1; min-width: 120px; text-align: center;
  }
  .stat-val { font-size: 24px; font-weight: 800; color: #111827; line-height: 1; }
  .stat-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: #9ca3af; margin-top: 6px; }
  .stat-card.s-effort  { border-color: #e5e7eb; }
  .stat-card.s-urgent  { border-color: #fed7aa; } .stat-card.s-urgent  .stat-val { color: #ea580c; }
  .stat-card.s-high    { border-color: #fca5a5; } .stat-card.s-high    .stat-val { color: #ef4444; }
  .stat-card.s-normal  { border-color: #fcd34d; } .stat-card.s-normal  .stat-val { color: #d97706; }
  .stat-card.s-low     { border-color: #93c5fd; } .stat-card.s-low     .stat-val { color: #3b82f6; }

  /* DESCRIPTION POPOVER */
  #desc-popover {
    display: none; position: fixed; z-index: 350;
    background: #fff; border: 1.5px solid #e5e7eb; border-radius: 12px;
    box-shadow: 0 6px 28px rgba(0,0,0,0.13); padding: 16px 18px;
    max-width: 300px; min-width: 200px;
  }
  .desc-pop-name { font-size: 13px; font-weight: 700; color: #111827; margin-bottom: 8px; line-height: 1.4; }
  .desc-pop-text { font-size: 13px; color: #6b7280; line-height: 1.65; }
  .desc-pop-foot { margin-top: 12px; padding-top: 10px; border-top: 1px solid #f3f4f6; display: flex; justify-content: flex-end; }

  /* TOAST */
  .toast { position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%) translateY(10px); background: #111827; color: #fff; border-radius: 9px; padding: 11px 20px; font-size: 13px; font-weight: 500; opacity: 0; transition: all 0.22s; z-index: 500; white-space: nowrap; }
  .toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

  @media (max-width: 600px) {
    nav { padding: 12px 14px; }
    .nav-tab { padding: 6px 11px; font-size: 12px; }
    .view { padding: 22px 14px; }
    table { font-size: 13px; }
    thead th, tbody td { padding: 10px 10px; }
    .td-desc { display: none; }
    .page-heading-actions { flex-direction: column; }
  }
</style>
</head>
<body>

<nav>
  <span class="nav-brand">Sprint Planner</span>
  <div class="nav-tabs">
    <button class="nav-tab active" onclick="showView('tasks')">Tasks</button>
    <button class="nav-tab" onclick="showView('dashboard')">Manager Dashboard</button>
    <button class="nav-tab" onclick="showView('settings')">Settings</button>
  </div>
</nav>

<!-- ===== TASKS VIEW ===== -->
<div class="view active" id="view-tasks">
  <div class="page-heading">
    <div class="page-heading-text">
      <h1>Tasks</h1>
      <p>All submitted tasks. Click a row to view details, or use the actions to edit or delete.</p>
    </div>
    <div class="page-heading-actions">
      <button class="btn-ghost" onclick="openImportModal()">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M7 1v8M4 6l3 3 3-3M2 11h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Import from ClickUp
      </button>
      <button class="btn-primary" onclick="openCreatePopup()">+ Create Task</button>
    </div>
  </div>
  <div class="stats-row" id="stats-row"></div>

  <div class="table-wrap">
    <table id="tasks-table">
      <thead>
        <tr>
          <th>Task Name</th>
          <th>Description</th>
          <th>Category</th>
          <th>Department</th>
          <th>Priority</th>
          <th>Effort</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody id="tasks-tbody">
        <tr class="empty-row"><td colspan="8">Loading...</td></tr>
      </tbody>
    </table>
  </div>
</div>

<!-- ===== DASHBOARD VIEW ===== -->
<div class="view" id="view-dashboard">
  <div class="page-heading">
    <div class="page-heading-text">
      <h1>Manager Dashboard</h1>
      <p>Select tasks for the sprint, then lock it in.</p>
    </div>
  </div>

  <div class="effort-card">
    <div class="effort-top">
      <span class="effort-title-label">Effort Remaining</span>
      <span class="effort-numbers" id="d-remaining">70 <span>/ 70 hrs</span></span>
    </div>
    <div class="bar-track"><div class="bar-fill" id="d-bar" style="width:0%"></div></div>
    <div class="bar-sub">
      <span id="d-used">0 hrs used</span>
      <span id="d-pct">0% of capacity</span>
    </div>
  </div>

  <div id="lock-banner" style="display:none" class="lock-banner">
    <span style="font-size:16px">&#128274;</span>
    <div>
      <div class="lock-text">Sprint is locked</div>
      <div class="lock-sub">Go to Settings to unlock or clear the sprint.</div>
    </div>
  </div>

  <div class="filter-bar">
    <button class="filter-chip active" data-f="all"    onclick="setFilter('all')">All</button>
    <button class="filter-chip" data-f="urgent"        onclick="setFilter('urgent')">Urgent</button>
    <button class="filter-chip" data-f="high"          onclick="setFilter('high')">High</button>
    <button class="filter-chip" data-f="normal"        onclick="setFilter('normal')">Normal</button>
    <button class="filter-chip" data-f="low"           onclick="setFilter('low')">Low</button>
  </div>

  <div class="section-label">Tasks</div>
  <div class="task-list" id="task-list">
    <div style="color:#d1d5db;font-size:14px;padding:32px;text-align:center;border:1.5px dashed #e5e7eb;border-radius:10px">No tasks yet.</div>
  </div>
  <div class="sprint-submit-row" id="sprint-submit-row">
    <button class="btn-primary" onclick="confirmLock()">Lock Sprint</button>
  </div>
</div>

<!-- ===== SETTINGS VIEW ===== -->
<div class="view" id="view-settings">
  <div class="page-heading">
    <div class="page-heading-text"><h1>Settings</h1><p>Manage sprint capacity, categories, departments, and sprint state.</p></div>
  </div>
  <div class="card">

    <div class="settings-block">
      <div class="settings-block-title">Sprint Capacity</div>
      <div class="settings-block-desc">Set the total effort hours available for this sprint.</div>
      <div class="settings-row">
        <input type="number" id="cap-input" value="70" min="1" step="1" style="width:110px" />
        <span style="font-size:14px;color:#6b7280;font-weight:500">hours</span>
        <button class="btn-primary" onclick="updateCap()">Save</button>
      </div>
    </div>

    <hr class="divider">

    <div class="settings-block">
      <div class="settings-block-title">Categories</div>
      <div class="settings-block-desc">Add categories that can be selected when creating tasks. Click any category chip in the task table to quickly reassign.</div>
      <div class="cat-list" id="cat-list"></div>
      <div class="cat-add-row">
        <input type="text" id="cat-input" placeholder="e.g. Frontend, Backend, Design..." onkeydown="if(event.key==='Enter')addCategory()" />
        <button class="btn-primary" onclick="addCategory()">Add</button>
      </div>
    </div>

    <hr class="divider">

    <div class="settings-block">
      <div class="settings-block-title">Departments</div>
      <div class="settings-block-desc">Add departments that can be assigned to tasks (e.g. Operations, CS, Product). Click any department chip in the task table to quickly reassign.</div>
      <div class="cat-list" id="dept-list"></div>
      <div class="cat-add-row">
        <input type="text" id="dept-input" placeholder="e.g. Operations, CS, Product..." onkeydown="if(event.key==='Enter')addDepartment()" />
        <button class="btn-primary" onclick="addDepartment()">Add</button>
      </div>
    </div>

    <hr class="divider">

    <div class="settings-block">
      <div class="settings-block-title">Unlock Sprint</div>
      <div class="settings-block-desc">Sprint is currently <span id="sprint-state-text" style="font-weight:700;color:#16a34a">unlocked</span>. Unlocking allows task selection to be changed.</div>
      <button class="btn-ghost" id="unlock-btn" onclick="unlockSprint()" style="display:none">Unlock Sprint</button>
      <span id="already-unlocked-msg" style="font-size:13px;color:#9ca3af">Sprint is already unlocked.</span>
    </div>

    <hr class="divider">

    <div class="settings-block">
      <div class="settings-block-title">Clear Sprint</div>
      <div class="settings-block-desc">Permanently deletes all tasks from this system and unlocks the sprint, so you can import the next sprint from ClickUp. Nothing is changed or deleted in ClickUp.</div>
      <button class="btn-danger" onclick="openConfirm('clear')">Clear Sprint &amp; Delete All Tasks</button>
    </div>

  </div>
</div>

<!-- ===== CREATE / EDIT TASK POPUP ===== -->
<div class="backdrop" id="task-popup-backdrop" onclick="closePopupBg(event,'task-popup-backdrop')">
  <div class="popup">
    <button class="popup-close" onclick="closeTaskPopup()">&#215;</button>
    <div class="popup-title" id="task-popup-title">Create Task</div>
    <div class="form-group">
      <label>Task Name</label>
      <input type="text" id="p-name" placeholder="e.g. Redesign checkout flow" />
    </div>
    <div class="form-group">
      <label>Description</label>
      <textarea id="p-desc" placeholder="What needs to be done and why?"></textarea>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">
      <div class="form-group">
        <label>Effort (hrs)</label>
        <input type="number" id="p-effort" placeholder="e.g. 8" min="0.5" step="0.5" />
      </div>
      <div class="form-group">
        <label>Category</label>
        <select id="p-cat"><option value="">-- Select --</option></select>
      </div>
      <div class="form-group">
        <label>Department</label>
        <select id="p-dept"><option value="">-- Select --</option></select>
      </div>
    </div>
    <div class="form-group">
      <label>Priority</label>
      <div class="priority-row">
        <div class="priority-btn" data-p="low"    onclick="selectPriority('low')">Low</div>
        <div class="priority-btn sel-normal" data-p="normal" onclick="selectPriority('normal')">Normal</div>
        <div class="priority-btn" data-p="high"   onclick="selectPriority('high')">High</div>
        <div class="priority-btn" data-p="urgent" onclick="selectPriority('urgent')">Urgent</div>
      </div>
    </div>
    <div class="popup-actions">
      <button class="btn-ghost" onclick="closeTaskPopup()">Cancel</button>
      <button class="btn-primary" id="task-popup-save-btn" onclick="saveTask()">Create Task</button>
    </div>
  </div>
</div>

<!-- ===== DETAIL VIEW POPUP ===== -->
<div class="backdrop" id="detail-backdrop" onclick="closePopupBg(event,'detail-backdrop')">
  <div class="popup" style="max-width:520px">
    <button class="popup-close" onclick="closeDetail()">&#215;</button>
    <div class="popup-title" id="det-name"></div>
    <div class="modal-sec-label">Description</div>
    <div class="modal-desc-text" id="det-desc"></div>
    <div class="modal-meta">
      <div class="meta-box"><div class="meta-key">Effort</div><div class="meta-val" id="det-effort"></div></div>
      <div class="meta-box"><div class="meta-key">Priority</div><div class="meta-val" id="det-priority"></div></div>
      <div class="meta-box"><div class="meta-key">Category</div><div class="meta-val" id="det-tag"></div></div>
      <div class="meta-box"><div class="meta-key">Department</div><div class="meta-val" id="det-dept"></div></div>
      <div class="meta-box" style="grid-column:span 2"><div class="meta-key">Status</div><div class="meta-val" id="det-status"></div></div>
    </div>
    <div class="popup-actions" id="det-actions"></div>
  </div>
</div>

<!-- ===== CONFIRM POPUP ===== -->
<div class="backdrop" id="confirm-backdrop" onclick="closePopupBg(event,'confirm-backdrop')">
  <div class="popup confirm-popup">
    <button class="popup-close" onclick="closeConfirmPopup()">&#215;</button>
    <div class="popup-title" id="confirm-title">Are you sure?</div>
    <div class="confirm-msg" id="confirm-msg"></div>
    <div class="popup-actions">
      <button class="btn-ghost" onclick="closeConfirmPopup()">Cancel</button>
      <button class="btn-danger" id="confirm-action-btn">Confirm</button>
    </div>
  </div>
</div>

<!-- ===== IMPORT FROM CLICKUP POPUP ===== -->
<div class="backdrop" id="import-backdrop" onclick="closePopupBg(event,'import-backdrop')">
  <div class="popup import-popup">
    <button class="popup-close" onclick="closeImportModal()">&#215;</button>
    <div class="popup-title">Import from ClickUp</div>
    <div id="import-content"></div>
  </div>
</div>

<!-- Chip dropdown (shared, fixed position) -->
<div id="chip-dropdown"></div>

<!-- Description popover (dashboard) -->
<div id="desc-popover"></div>

<div class="toast" id="toast"></div>

<script>
  /* ---- STATE ---- */
  let tasks = [], capacity = 70, sprintLocked = false;
  let selectedPriority = 'normal', editingId = null, deletingId = null;
  let activeFilter = 'all', modalTaskId = null;
  let categories = [], departments = [];

  /* Import state */
  let importTasks = [], importSelected = new Set(), importListName = '';

  /* Chip dropdown state */
  let chipDropCtx = null;

  /* ---- HELPERS ---- */
  function esc(s) {
    return String(s)
      .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }
  function cap(s) { return s.charAt(0).toUpperCase() + s.slice(1); }
  function priLabel(p) {
    // treat legacy 'medium' as 'normal'
    if (p === 'medium') return 'Normal';
    return cap(p);
  }
  function priClass(p) { return p === 'medium' ? 'normal' : p; }
  function toast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg; t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2800);
  }
  const CHECK_SVG = '<svg width="10" height="8" viewBox="0 0 10 8" fill="none"><path d="M1 4l3 3 5-6" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

  /* ---- API ---- */
  async function api(action, data) {
    const opts = data !== undefined
      ? { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(data) }
      : { method: 'GET' };
    const res = await fetch(`api.php?action=${action}`, opts);
    if (!res.ok) { toast('Server error.'); throw new Error(await res.text()); }
    return res.json();
  }

  /* ---- NORMALISE DB ROW -> JS OBJECT ---- */
  function normTask(t) {
    let pri = t.priority || 'normal';
    if (pri === 'medium') pri = 'normal';
    return {
      id:       parseInt(t.id),
      name:     t.name,
      desc:     t.description || 'No description provided.',
      effort:   parseFloat(t.effort),
      tag:      t.category   || 'General',
      dept:     t.department || 'General',
      priority: pri,
      accepted: parseInt(t.accepted) === 1,
    };
  }

  /* ---- INIT ---- */
  async function init() {
    const data   = await api('init');
    tasks        = data.tasks.map(normTask);
    categories   = data.categories;
    departments  = data.departments;
    capacity     = data.capacity;
    sprintLocked = !!data.locked;
    document.getElementById('cap-input').value = capacity;
    renderTable();
    renderSettings();
    renderCatList();
    renderDeptList();
  }

  /* ---- VIEWS ---- */
  function showView(v) {
    document.querySelectorAll('.view').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.nav-tab').forEach((t, i) => t.classList.toggle('active',
      (i===0&&v==='tasks') || (i===1&&v==='dashboard') || (i===2&&v==='settings')));
    document.getElementById('view-' + v).classList.add('active');
    if (v === 'dashboard') { renderDashboard(); updateMeter(); }
    if (v === 'settings')  { document.getElementById('cap-input').value = capacity; renderSettings(); renderCatList(); renderDeptList(); }
    if (v === 'tasks')     { renderTable(); }
  }

  /* ---- PRIORITY ---- */
  function selectPriority(p) {
    selectedPriority = p;
    document.querySelectorAll('#task-popup-backdrop .priority-btn').forEach(b => {
      b.className = 'priority-btn';
      if (b.dataset.p === p) b.classList.add('sel-' + p);
    });
  }

  /* ---- TASK POPUP ---- */
  function openCreatePopup() {
    editingId = null;
    selectedPriority = 'normal';
    document.getElementById('task-popup-title').textContent    = 'Create Task';
    document.getElementById('task-popup-save-btn').textContent = 'Create Task';
    document.getElementById('p-name').value   = '';
    document.getElementById('p-desc').value   = '';
    document.getElementById('p-effort').value = '';
    selectPriority('normal');
    populateCatSelect(); populateDeptSelect();
    document.getElementById('p-cat').value  = '';
    document.getElementById('p-dept').value = '';
    document.getElementById('task-popup-backdrop').classList.add('open');
  }

  function openEditPopup(id) {
    const t = tasks.find(x => x.id === id);
    if (!t) return;
    editingId = id;
    selectedPriority = t.priority;
    document.getElementById('task-popup-title').textContent    = 'Edit Task';
    document.getElementById('task-popup-save-btn').textContent = 'Save Changes';
    document.getElementById('p-name').value   = t.name;
    document.getElementById('p-desc').value   = t.desc === 'No description provided.' ? '' : t.desc;
    document.getElementById('p-effort').value = t.effort;
    selectPriority(t.priority);
    populateCatSelect(); populateDeptSelect();
    document.getElementById('p-cat').value  = t.tag;
    document.getElementById('p-dept').value = t.dept;
    document.getElementById('task-popup-backdrop').classList.add('open');
  }

  function populateCatSelect() {
    const sel = document.getElementById('p-cat');
    const cur = sel.value;
    sel.innerHTML = '<option value="">-- Select --</option>' +
      categories.map(c => `<option value="${esc(c)}">${esc(c)}</option>`).join('');
    sel.value = cur;
  }

  function populateDeptSelect() {
    const sel = document.getElementById('p-dept');
    const cur = sel.value;
    sel.innerHTML = '<option value="">-- Select --</option>' +
      departments.map(d => `<option value="${esc(d)}">${esc(d)}</option>`).join('');
    sel.value = cur;
  }

  function closeTaskPopup() {
    document.getElementById('task-popup-backdrop').classList.remove('open');
    editingId = null;
  }

  async function saveTask() {
    const name   = document.getElementById('p-name').value.trim();
    const desc   = document.getElementById('p-desc').value.trim();
    const effort = parseFloat(document.getElementById('p-effort').value);
    const tag    = document.getElementById('p-cat').value;
    const dept   = document.getElementById('p-dept').value;
    if (!name)           { toast('Task name is required.'); return; }
    if (!effort || effort <= 0) { toast('Enter a valid effort estimate.'); return; }

    const payload = { name, desc: desc||'', effort, tag: tag||'General', department: dept||'General', priority: selectedPriority };

    if (editingId !== null) {
      payload.id = editingId;
      const res = await api('update_task', payload);
      if (res.success) {
        const idx = tasks.findIndex(x => x.id === editingId);
        if (idx !== -1) tasks[idx] = normTask(res.task);
        toast('Task updated.');
      }
    } else {
      const res = await api('create_task', payload);
      if (res.success) { tasks.push(normTask(res.task)); toast('Task created.'); }
    }
    closeTaskPopup();
    renderTable();
  }

  /* ---- STATS CARDS ---- */
  function renderStats() {
    const el = document.getElementById('stats-row');
    if (!tasks.length) { el.innerHTML = ''; return; }
    const totalEffort = tasks.reduce((s, t) => s + t.effort, 0);
    const fmt = n => n % 1 === 0 ? n : n.toFixed(1);
    const c = { urgent: 0, high: 0, normal: 0, low: 0 };
    tasks.forEach(t => { if (c[t.priority] !== undefined) c[t.priority]++; });
    el.innerHTML = `
      <div class="stat-card">
        <div class="stat-val">${tasks.length}</div>
        <div class="stat-label">Total Tasks</div>
      </div>
      <div class="stat-card s-effort">
        <div class="stat-val">${fmt(totalEffort)} hrs</div>
        <div class="stat-label">Total Effort</div>
      </div>
      <div class="stat-card s-urgent">
        <div class="stat-val">${c.urgent}</div>
        <div class="stat-label">Urgent</div>
      </div>
      <div class="stat-card s-high">
        <div class="stat-val">${c.high}</div>
        <div class="stat-label">High</div>
      </div>
      <div class="stat-card s-normal">
        <div class="stat-val">${c.normal}</div>
        <div class="stat-label">Normal</div>
      </div>
      <div class="stat-card s-low">
        <div class="stat-val">${c.low}</div>
        <div class="stat-label">Low</div>
      </div>`;
  }

  /* ---- TABLE ---- */
  function renderTable() {
    renderStats();
    const tbody = document.getElementById('tasks-tbody');
    if (!tasks.length) {
      tbody.innerHTML = '<tr class="empty-row"><td colspan="8">No tasks yet. Click "Create Task" to add one.</td></tr>';
      return;
    }
    const pc = { low:'badge-low', normal:'badge-normal', high:'badge-high', urgent:'badge-urgent' };
    tbody.innerHTML = tasks.map(t => `
      <tr>
        <td class="td-name">${esc(t.name)}</td>
        <td class="td-desc">${esc(t.desc)}</td>
        <td>
          <span class="cat-chip" onclick="openChipDropdown(event,${t.id},'category')" title="Click to change category">
            ${esc(t.tag)}<span class="chip-arrow">▾</span>
          </span>
        </td>
        <td>
          <span class="dept-chip" onclick="openChipDropdown(event,${t.id},'department')" title="Click to change department">
            ${esc(t.dept)}<span class="chip-arrow">▾</span>
          </span>
        </td>
        <td><span class="badge ${pc[priClass(t.priority)] || 'badge-normal'}">${priLabel(t.priority)}</span></td>
        <td>${t.effort} hrs</td>
        <td><span class="badge ${t.accepted ? 'badge-accepted' : 'badge-pending'}">${t.accepted ? 'Accepted' : 'Pending'}</span></td>
        <td>
          <div class="td-actions">
            <button class="tbl-btn" onclick="openDetailFromTable(${t.id})">View</button>
            <button class="tbl-btn" onclick="openEditPopup(${t.id})">Edit</button>
            <button class="tbl-btn del" onclick="openConfirm('delete',${t.id})">Delete</button>
          </div>
        </td>
      </tr>`).join('');
  }

  /* ---- CHIP DROPDOWN (inline cat/dept picker) ---- */
  function openChipDropdown(event, taskId, field) {
    event.stopPropagation();
    const el   = event.currentTarget;
    const rect = el.getBoundingClientRect();
    const t    = tasks.find(x => x.id === taskId);
    if (!t) return;

    const options = field === 'category' ? categories : departments;
    const current = field === 'category' ? t.tag : t.dept;

    const dd = document.getElementById('chip-dropdown');
    if (!options.length) {
      dd.innerHTML = `<div class="chip-dd-empty">No options yet.<br>Add them in Settings.</div>`;
    } else {
      dd.innerHTML = options.map(opt =>
        `<div class="chip-dd-item ${opt === current ? 'dd-active' : ''}" data-val="${esc(opt)}"
              onclick="selectChipOption(${taskId},'${field}',this.dataset.val)">${esc(opt)}</div>`
      ).join('');
    }

    // Position below the chip, keep in viewport
    dd.style.display = 'block';
    let top  = rect.bottom + window.scrollY + 4;
    let left = rect.left   + window.scrollX;
    const ddW = dd.offsetWidth, ddH = dd.offsetHeight;
    if (left + ddW > window.innerWidth  - 8) left = window.innerWidth  - ddW - 8;
    if (top  + ddH > window.innerHeight + window.scrollY - 8) top = rect.top + window.scrollY - ddH - 4;
    dd.style.top  = top  + 'px';
    dd.style.left = left + 'px';

    chipDropCtx = { taskId, field };
  }

  async function selectChipOption(taskId, field, value) {
    const t = tasks.find(x => x.id === taskId);
    if (!t) return;
    if (field === 'category')   t.tag  = value;
    else                        t.dept = value;
    closeChipDropdown();
    await api('update_task', { id: t.id, name: t.name, desc: t.desc, tag: t.tag, department: t.dept, priority: t.priority, effort: t.effort });
    renderTable();
    toast(`${field === 'category' ? 'Category' : 'Department'} updated to "${value}".`);
  }

  function closeChipDropdown() {
    const dd = document.getElementById('chip-dropdown');
    dd.style.display = 'none';
    chipDropCtx = null;
  }

  /* ---- DESCRIPTION POPOVER (dashboard) ---- */
  function openDescPopover(event, id) {
    event.stopPropagation();
    const t = tasks.find(x => x.id === id);
    if (!t) return;

    const btn  = event.currentTarget;
    const rect = btn.getBoundingClientRect();
    const pop  = document.getElementById('desc-popover');

    pop.innerHTML = `
      <div class="desc-pop-name">${esc(t.name)}</div>
      <div class="desc-pop-text">${esc(t.desc)}</div>
      <div class="desc-pop-foot">
        <button class="btn-ghost" style="padding:5px 14px;font-size:12px"
                onclick="closeDescPopover();openDetail(${id},true)">Full Details</button>
      </div>`;

    pop.style.display = 'block';
    // Position: try above the button first, fall back to below
    const pw = pop.offsetWidth, ph = pop.offsetHeight;
    let top  = rect.top  + window.scrollY - ph - 8;
    let left = rect.right + window.scrollX - pw;
    if (top < window.scrollY + 8) top = rect.bottom + window.scrollY + 8;
    if (left < 8) left = 8;
    if (left + pw > window.innerWidth - 8) left = window.innerWidth - pw - 8;
    pop.style.top  = top  + 'px';
    pop.style.left = left + 'px';
  }

  function closeDescPopover() {
    document.getElementById('desc-popover').style.display = 'none';
  }

  /* ---- DETAIL POPUP ---- */
  function openDetailFromTable(id) { openDetail(id, false); }
  function openDetailFromDash(e, id) { e.stopPropagation(); openDetail(id, true); }

  function openDetail(id, fromDash) {
    modalTaskId = id;
    refreshDetail(id, fromDash);
    document.getElementById('detail-backdrop').classList.add('open');
  }

  function refreshDetail(id, fromDash) {
    const t = tasks.find(x => x.id === id);
    if (!t) return;
    const pc = { low:'#3b82f6', normal:'#d97706', medium:'#d97706', high:'#ef4444', urgent:'#ea580c' };
    document.getElementById('det-name').textContent    = t.name;
    document.getElementById('det-desc').textContent    = t.desc;
    document.getElementById('det-effort').textContent  = t.effort + ' hrs';
    document.getElementById('det-priority').innerHTML  = `<span style="color:${pc[t.priority]||'#d97706'}">${priLabel(t.priority)}</span>`;
    document.getElementById('det-tag').textContent     = t.tag;
    document.getElementById('det-dept').textContent    = t.dept;
    document.getElementById('det-status').innerHTML    = t.accepted
      ? '<span style="color:#16a34a">Accepted</span>'
      : '<span style="color:#d97706">Pending</span>';
    const actions = document.getElementById('det-actions');
    if (fromDash && !sprintLocked) {
      actions.innerHTML = t.accepted
        ? `<button class="btn-primary" style="background:#ef4444" onclick="modalToggle()">Remove from Sprint</button><button class="btn-ghost" onclick="closeDetail()">Close</button>`
        : `<button class="btn-primary" onclick="modalToggle()">Accept Task</button><button class="btn-ghost" onclick="closeDetail()">Close</button>`;
    } else {
      actions.innerHTML = `<button class="btn-ghost" onclick="closeDetail()">Close</button>`;
    }
  }

  async function modalToggle() {
    if (modalTaskId === null) return;
    const res = await api('toggle_accept', { id: modalTaskId });
    if (!res.success) { toast(res.error || 'Not enough capacity.'); return; }
    const t = tasks.find(x => x.id === modalTaskId);
    if (t) t.accepted = res.accepted;
    renderDashboard(); renderTable(); updateMeter();
    refreshDetail(modalTaskId, true);
  }

  function closeDetail() { document.getElementById('detail-backdrop').classList.remove('open'); modalTaskId = null; }

  /* ---- CONFIRM POPUP ---- */
  function openConfirm(type, id) {
    if (type === 'delete') {
      deletingId = id;
      document.getElementById('confirm-title').textContent = 'Delete Task?';
      document.getElementById('confirm-msg').textContent   = 'This task will be permanently removed and cannot be undone.';
      document.getElementById('confirm-action-btn').onclick = () => deleteTask();
    } else if (type === 'lock') {
      document.getElementById('confirm-title').textContent = 'Lock Sprint?';
      document.getElementById('confirm-msg').textContent   = 'The selected tasks will be locked into the sprint. No changes can be made until you unlock from Settings.';
      document.getElementById('confirm-action-btn').onclick = () => lockSprint();
    } else if (type === 'clear') {
      document.getElementById('confirm-title').textContent = 'Clear Sprint & Delete All Tasks?';
      document.getElementById('confirm-msg').textContent   = 'All tasks will be permanently deleted from this system so you can import the next sprint. This only affects your local database — nothing is changed or deleted in ClickUp. This cannot be undone.';
      document.getElementById('confirm-action-btn').onclick = () => clearSprint();
    }
    document.getElementById('confirm-backdrop').classList.add('open');
  }

  function closeConfirmPopup() { document.getElementById('confirm-backdrop').classList.remove('open'); deletingId = null; }

  async function deleteTask() {
    if (deletingId === null) return;
    const res = await api('delete_task', { id: deletingId });
    if (res.success) {
      tasks = tasks.filter(x => x.id !== deletingId);
      closeConfirmPopup();
      renderTable(); renderDashboard(); updateMeter();
      toast('Task deleted.');
    }
  }

  /* ---- DASHBOARD ---- */
  function setFilter(f) {
    activeFilter = f;
    document.querySelectorAll('.filter-chip').forEach(c => c.classList.toggle('active', c.dataset.f === f));
    renderDashboard();
  }

  function renderDashboard() {
    const list = document.getElementById('task-list');
    document.getElementById('lock-banner').style.display       = sprintLocked ? 'flex' : 'none';
    document.getElementById('sprint-submit-row').style.display = sprintLocked ? 'none' : 'flex';
    const filtered = activeFilter === 'all' ? tasks
      : tasks.filter(t => t.priority === activeFilter || (activeFilter === 'normal' && t.priority === 'medium'));
    if (!filtered.length) {
      list.innerHTML = '<div style="color:#d1d5db;font-size:14px;padding:32px;text-align:center;border:1.5px dashed #e5e7eb;border-radius:10px">No tasks yet.</div>';
      return;
    }
    const usedSoFar = tasks.filter(t => t.accepted).reduce((s, t) => s + t.effort, 0);
    list.innerHTML = filtered.map(t => {
      const exceed = !t.accepted && (usedSoFar + t.effort) > capacity;
      return `
      <div class="task-card ${t.accepted?'selected':''} ${exceed?'over-budget':''} ${sprintLocked?'locked-card':''}"
           onclick="${!sprintLocked && !exceed ? `toggleAccept(${t.id})` : 'void 0'}">
        ${exceed ? '<span class="over-label">Over Budget</span>' : ''}
        <div class="tc-check">${t.accepted ? CHECK_SVG : ''}</div>
        <div class="tc-dot ${priClass(t.priority)}"></div>
        <div class="tc-body">
          <div class="tc-name">${esc(t.name)}</div>
          <div class="tc-meta">
            <span class="tc-tag">${esc(t.tag)}</span>
            <span class="tc-dept">${esc(t.dept)}</span>
          </div>
        </div>
        <div class="tc-hrs">${t.effort} hrs</div>
        <button class="tc-info" onclick="openDescPopover(event,${t.id})" title="Show description">i</button>
      </div>`;
    }).join('');
  }

  async function toggleAccept(id) {
    if (sprintLocked) return;
    const res = await api('toggle_accept', { id });
    if (!res.success) { toast(res.error || 'Not enough capacity.'); return; }
    const t = tasks.find(x => x.id === id);
    if (t) t.accepted = res.accepted;
    renderDashboard(); renderTable(); updateMeter();
  }

  function updateMeter() {
    const used      = tasks.filter(t => t.accepted).reduce((s, t) => s + t.effort, 0);
    const remaining = Math.max(0, capacity - used);
    const pct       = Math.min(100, (used / capacity) * 100);
    const fmt       = n => n % 1 === 0 ? n : n.toFixed(1);
    document.getElementById('d-remaining').innerHTML = `${fmt(remaining)} <span>/ ${capacity} hrs</span>`;
    document.getElementById('d-used').textContent    = `${fmt(used)} hrs used`;
    document.getElementById('d-pct').textContent     = `${Math.round(pct)}% of capacity`;
    const bar = document.getElementById('d-bar');
    bar.style.width = pct + '%';
    bar.className   = 'bar-fill' + (pct >= 100 ? ' over' : pct >= 75 ? ' warn' : '');
  }

  function confirmLock() {
    if (!tasks.filter(t => t.accepted).length) { toast('Select at least one task before locking.'); return; }
    openConfirm('lock');
  }

  async function lockSprint() {
    await api('lock_sprint');
    sprintLocked = true;
    closeConfirmPopup(); renderDashboard(); renderSettings();
    toast('Sprint locked.');
  }

  async function unlockSprint() {
    await api('unlock_sprint');
    sprintLocked = false;
    renderDashboard(); renderSettings();
    toast('Sprint unlocked.');
  }

  async function clearSprint() {
    await api('clear_sprint');
    tasks = [];        // all tasks deleted from DB — local array cleared too
    sprintLocked = false;
    closeConfirmPopup(); renderDashboard(); renderTable(); updateMeter(); renderSettings();
    toast('Sprint cleared. All tasks deleted. Ready for next import.');
  }

  /* ---- SETTINGS ---- */
  async function updateCap() {
    const v = parseFloat(document.getElementById('cap-input').value);
    if (!v || v <= 0) { toast('Enter a valid capacity.'); return; }
    await api('update_capacity', { capacity: v });
    capacity = v; updateMeter();
    toast('Capacity saved.');
  }

  function renderSettings() {
    const stateText  = document.getElementById('sprint-state-text');
    const unlockBtn  = document.getElementById('unlock-btn');
    const alreadyMsg = document.getElementById('already-unlocked-msg');
    if (sprintLocked) {
      stateText.textContent = 'locked'; stateText.style.color = '#ef4444';
      unlockBtn.style.display = 'inline-flex'; alreadyMsg.style.display = 'none';
    } else {
      stateText.textContent = 'unlocked'; stateText.style.color = '#16a34a';
      unlockBtn.style.display = 'none'; alreadyMsg.style.display = 'block';
    }
  }

  /* ---- CATEGORIES ---- */
  function renderCatList() {
    const el = document.getElementById('cat-list');
    if (!categories.length) { el.innerHTML = '<span style="font-size:13px;color:#d1d5db">No categories yet.</span>'; return; }
    el.innerHTML = categories.map((c, i) => `
      <div class="cat-item">
        ${esc(c)}
        <button class="cat-remove" onclick="removeCategory(${i})" title="Remove">&#215;</button>
      </div>`).join('');
  }

  async function addCategory() {
    const input = document.getElementById('cat-input');
    const val   = input.value.trim();
    if (!val) return;
    if (categories.map(c => c.toLowerCase()).includes(val.toLowerCase())) { toast('Category already exists.'); return; }
    const res = await api('add_category', { name: val });
    if (res.success) { categories = res.categories; input.value = ''; renderCatList(); toast('Category added.'); }
  }

  async function removeCategory(i) {
    const name = categories[i];
    const res  = await api('remove_category', { name });
    if (res.success) { categories = res.categories; renderCatList(); }
  }

  /* ---- DEPARTMENTS ---- */
  function renderDeptList() {
    const el = document.getElementById('dept-list');
    if (!departments.length) { el.innerHTML = '<span style="font-size:13px;color:#d1d5db">No departments yet.</span>'; return; }
    el.innerHTML = departments.map((d, i) => `
      <div class="cat-item dept-item">
        ${esc(d)}
        <button class="cat-remove" onclick="removeDepartment(${i})" title="Remove">&#215;</button>
      </div>`).join('');
  }

  async function addDepartment() {
    const input = document.getElementById('dept-input');
    const val   = input.value.trim();
    if (!val) return;
    if (departments.map(d => d.toLowerCase()).includes(val.toLowerCase())) { toast('Department already exists.'); return; }
    const res = await api('add_department', { name: val });
    if (res.success) { departments = res.departments; input.value = ''; renderDeptList(); toast('Department added.'); }
  }

  async function removeDepartment(i) {
    const name = departments[i];
    const res  = await api('remove_department', { name });
    if (res.success) { departments = res.departments; renderDeptList(); }
  }

  /* ---- IMPORT FROM CLICKUP ---- */
  async function openImportModal() {
    importTasks = []; importSelected = new Set(); importListName = '';
    document.getElementById('import-backdrop').classList.add('open');
    renderImportLoading();
    try {
      const res = await api('clickup_fetch');
      if (!res.success) { renderImportError(res.error || 'Failed to fetch from ClickUp.'); return; }
      importTasks    = res.tasks;
      importListName = res.list_name || 'Backlog';
      importTasks.forEach((t, i) => { if (!t.already_imported) importSelected.add(i); });
      renderImportTaskList();
    } catch (e) {
      renderImportError(e.message || 'Network error.');
    }
  }

  function closeImportModal() {
    document.getElementById('import-backdrop').classList.remove('open');
    importTasks = []; importSelected = new Set(); importListName = '';
  }

  function renderImportLoading() {
    document.getElementById('import-content').innerHTML = `
      <div class="import-loading">
        <div class="spinner"></div>
        <div class="import-loading-text">Fetching Ready for Sprint tasks from ClickUp…</div>
      </div>`;
  }

  function renderImportError(msg) {
    document.getElementById('import-content').innerHTML = `
      <div class="import-error-box">${esc(msg)}</div>
      <div class="popup-actions">
        <button class="btn-ghost" onclick="closeImportModal()">Close</button>
        <button class="btn-primary" onclick="openImportModal()">Retry</button>
      </div>`;
  }

  function renderImportTaskList() {
    const total    = importTasks.length;
    const selCount = importSelected.size;

    document.getElementById('import-content').innerHTML = `
      <div class="import-subtitle">${esc(importListName)} — Ready for Sprint &nbsp;·&nbsp; ${total} task${total!==1?'s':''} found</div>
      <div class="import-note">Effort is taken from ClickUp's time estimate (in hrs). Tasks showing 1 hr have no estimate set in ClickUp — edit after importing.</div>
      <div class="import-ctrl-bar">
        <button class="btn-ghost" style="padding:6px 14px;font-size:12px" onclick="importSelectAll()">Select All</button>
        <button class="btn-ghost" style="padding:6px 14px;font-size:12px" onclick="importDeselectAll()">Deselect All</button>
      </div>
      <div class="import-task-list">
        ${total === 0
          ? '<div style="padding:32px;text-align:center;color:#d1d5db;font-size:14px">No tasks with "Ready for Sprint" status found.</div>'
          : importTasks.map((t, i) => {
              const on      = importSelected.has(i);
              const effortTxt = t.effort !== null ? `${t.effort} hr${t.effort!==1?'s':''}` : '—';
              return `
              <div class="import-task-item ${on?'sel-import':''} ${t.already_imported?'already-done':''}" data-idx="${i}"
                   onclick="${t.already_imported?'':(`toggleImportItem(${i})`)}">
                <div class="import-chk">${on ? CHECK_SVG : ''}</div>
                <div class="tc-dot ${t.priority}"></div>
                <div class="import-task-name" title="${esc(t.name)}">${esc(t.name)}</div>
                <div class="import-task-right">
                  <span class="pri-badge-sm ${t.priority}">${cap(t.priority)}</span>
                  ${t.already_imported
                    ? '<span class="already-badge">Imported</span>'
                    : `<span class="import-effort">${effortTxt}</span>`}
                </div>
              </div>`}).join('')}
      </div>
      <div class="import-footer">
        <span class="import-count"><strong>${selCount}</strong> of ${total} selected</span>
        <div style="display:flex;gap:8px">
          <button class="btn-ghost" onclick="closeImportModal()">Cancel</button>
          <button class="btn-primary" id="import-do-btn" onclick="doImport()" ${selCount===0?'disabled':''}>
            Import ${selCount>0 ? selCount+' Task'+(selCount!==1?'s':'') : 'Tasks'}
          </button>
        </div>
      </div>`;
  }

  /* Toggle a single import item without re-rendering the whole list */
  function toggleImportItem(i) {
    if (importTasks[i]?.already_imported) return;
    if (importSelected.has(i)) importSelected.delete(i);
    else importSelected.add(i);

    const el = document.querySelector(`.import-task-item[data-idx="${i}"]`);
    if (el) {
      const on = importSelected.has(i);
      el.classList.toggle('sel-import', on);
      const chk = el.querySelector('.import-chk');
      if (chk) chk.innerHTML = on ? CHECK_SVG : '';
    }
    updateImportFooter();
  }

  function updateImportFooter() {
    const selCount = importSelected.size;
    const countEl  = document.querySelector('.import-count');
    const btnEl    = document.getElementById('import-do-btn');
    if (countEl) countEl.innerHTML = `<strong>${selCount}</strong> of ${importTasks.length} selected`;
    if (btnEl) {
      btnEl.disabled    = selCount === 0;
      btnEl.textContent = selCount > 0
        ? `Import ${selCount} Task${selCount!==1?'s':''}`
        : 'Import Tasks';
    }
  }

  function importSelectAll() {
    importTasks.forEach((t, i) => { if (!t.already_imported) importSelected.add(i); });
    document.querySelectorAll('.import-task-item:not(.already-done)').forEach(el => {
      el.classList.add('sel-import');
      const chk = el.querySelector('.import-chk');
      if (chk) chk.innerHTML = CHECK_SVG;
    });
    updateImportFooter();
  }

  function importDeselectAll() {
    importSelected.clear();
    document.querySelectorAll('.import-task-item:not(.already-done)').forEach(el => {
      el.classList.remove('sel-import');
      const chk = el.querySelector('.import-chk');
      if (chk) chk.innerHTML = '';
    });
    updateImportFooter();
  }

  async function doImport() {
    const selected = Array.from(importSelected).map(i => importTasks[i]).filter(Boolean);
    if (!selected.length) { toast('No tasks selected.'); return; }

    const btn = document.getElementById('import-do-btn');
    if (btn) { btn.disabled = true; btn.textContent = 'Importing…'; }

    const payload = selected.map(t => ({
      cu_id:       t.cu_id,
      name:        t.name,
      description: t.description || '',
      priority:    t.priority,
      effort:      t.effort !== null ? t.effort : 1,
      tag:         'General',
      department:  'General',
    }));

    try {
      const res = await api('import_clickup_tasks', { tasks: payload });
      if (res.success) {
        tasks = res.tasks.map(normTask);
        closeImportModal();
        renderTable(); renderDashboard(); updateMeter();
        toast(`${res.imported} task${res.imported!==1?'s':''} imported successfully.`);
      } else {
        toast(res.error || 'Import failed.');
        if (btn) { btn.disabled = false; btn.textContent = 'Retry Import'; }
      }
    } catch (e) {
      toast('Import failed: ' + (e.message || 'Unknown error'));
      if (btn) { btn.disabled = false; btn.textContent = 'Retry Import'; }
    }
  }

  /* ---- POPUP BACKDROP CLOSE ---- */
  function closePopupBg(e, id) {
    if (e.target === document.getElementById(id)) {
      if (id === 'task-popup-backdrop') closeTaskPopup();
      else if (id === 'detail-backdrop')  closeDetail();
      else if (id === 'confirm-backdrop') closeConfirmPopup();
      else if (id === 'import-backdrop')  closeImportModal();
    }
  }

  /* Close floating elements on outside click */
  document.addEventListener('click', function(e) {
    if (!document.getElementById('chip-dropdown').contains(e.target)) closeChipDropdown();
    if (!document.getElementById('desc-popover').contains(e.target))  closeDescPopover();
  });

  init();
</script>
</body>
</html>
