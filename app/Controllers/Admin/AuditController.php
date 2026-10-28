<?php
require_once BASE_PATH . '/app/Core/BaseController.php';
require_once BASE_PATH . '/app/Models/AuditModel.php';
require_once BASE_PATH . '/app/Models/UserModel.php';

class AuditController extends BaseController
{
    public function index(): void
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $action = $_GET['action'] ?? null;
        $userId = (int)($_GET['user_id'] ?? 0) ?: null;
        $logs = AuditModel::listAll($page, 100, $action, $userId);
        $users = Database::fetchAll("SELECT id, username, role FROM users ORDER BY username");

        $this->view('admin/audit', 'admin', [
            'title' => 'Audit Logs',
            'logs' => $logs,
            'users' => $users, 'action' => $action, 'userId' => $userId,
            'page' => $page, 'csrf' => $this->csrfField(),
        ]);
    }
}
