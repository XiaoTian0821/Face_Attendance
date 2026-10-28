<?php
require_once BASE_PATH . '/app/Core/BaseController.php';
require_once BASE_PATH . '/app/Models/UserModel.php';
require_once BASE_PATH . '/app/Models/StudentModel.php';
require_once BASE_PATH . '/app/Models/TeacherModel.php';
require_once BASE_PATH . '/app/Models/AuditModel.php';

class UserController extends BaseController
{
    public function index(): void
    {
        $users = UserModel::listAll();
        $this->view('admin/users', 'admin', [
            'title' => 'User Management',
            'users' => $users, 'csrf' => $this->csrfField(),
        ]);
    }

    public function create(): void
    {
        $this->view('admin/user_form', 'admin', [
            'title' => 'Add User',
            'user' => null, 'csrf' => $this->csrfField(),
        ]);
    }

    public function store(): void
    {
        if (!self::verifyCsrf()) { $this->redirectFlash('/admin/users', 'error', 'Invalid CSRF'); return; }
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'student';
        $linkCode = trim($_POST['link_code'] ?? '');

        if (empty($username) || empty($email) || empty($password)) {
            $this->redirectFlash('/admin/users/create', 'error', 'Username, email, and password required.');
            return;
        }
        if (strlen($password) < 8) {
            $this->redirectFlash('/admin/users/create', 'error', 'Password must be at least 8 characters.');
            return;
        }
        if (!in_array($role, ['admin', 'teacher', 'student'])) {
            $this->redirectFlash('/admin/users/create', 'error', 'Invalid role.');
            return;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $userId = UserModel::create($username, $email, $hash, $role);

        // Link to student or teacher if code provided
        if ($role === 'student' && !empty($linkCode)) {
            $student = StudentModel::findByCode($linkCode);
            if ($student) {
                Database::update('students', ['user_id' => $userId], 'id = ?', [$student['id']]);
            }
        } elseif ($role === 'teacher' && !empty($linkCode)) {
            $teacher = TeacherModel::findByCode($linkCode);
            if ($teacher) {
                Database::update('teachers', ['user_id' => $userId], 'id = ?', [$teacher['id']]);
            }
        }

        AuditModel::log($userId, null, 'user_created', $this->getIp(), $this->getDevice(), $this->getBrowser(),
            null, null, ['username' => $username, 'role' => $role]);
        $this->redirectFlash('/admin/users', 'success', 'User created successfully.');
    }

    public function resetPassword(string $id): void
    {
        if (!self::verifyCsrf()) { $this->redirectFlash('/admin/users', 'error', 'Invalid CSRF'); return; }
        $id = (int)$id;
        $newPass = $_POST['new_password'] ?? '';
        if (strlen($newPass) < 8) {
            $this->redirectFlash('/admin/users', 'error', 'Password must be at least 8 characters.');
            return;
        }
        UserModel::resetPassword($id, password_hash($newPass, PASSWORD_DEFAULT));
        AuditModel::log(Session::get('user_id'), null, 'user_password_reset',
            $this->getIp(), $this->getDevice(), $this->getBrowser(), null, null, ['user_id' => $id]);
        $this->redirectFlash('/admin/users', 'success', 'Password reset complete.');
    }

    public function toggleStatus(string $id): void
    {
        if (!self::verifyCsrf()) { $this->redirectFlash('/admin/users', 'error', 'Invalid CSRF'); return; }
        $id = (int)$id;
        if ($id == Session::get('user_id')) {
            $this->redirectFlash('/admin/users', 'error', 'Cannot deactivate your own account.');
            return;
        }
        UserModel::toggleStatus($id);
        AuditModel::log(Session::get('user_id'), null, 'user_status_toggled',
            $this->getIp(), $this->getDevice(), $this->getBrowser(), null, null, ['user_id' => $id]);
        $this->redirectFlash('/admin/users', 'success', 'Status updated.');
    }
}
