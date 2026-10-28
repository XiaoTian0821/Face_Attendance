<?php
require_once BASE_PATH . '/app/Core/BaseController.php';

class AuthController extends BaseController
{
    public function showLogin(): void
    {
        $this->view('auth/login', 'auth', [
            'title' => 'Login',
            'csrf' => $this->csrfField(),
        ]);
    }

    public function login(): void
    {
        if (!self::verifyCsrf()) {
            $this->redirectFlash('/login', 'error', 'Invalid CSRF token');
            return;
        }

        $identifier = trim($_POST['identifier'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($identifier === '' || $password === '') {
            $this->redirectFlash('/login', 'error', 'Please enter username and password.');
            return;
        }

        $user = UserModel::findByUsername($identifier) ?: UserModel::findByEmail($identifier);

        if (!$user || !$user['password_hash'] || !password_verify($password, $user['password_hash'])) {
            $this->redirectFlash('/login', 'error', 'Invalid credentials. Please try again.');
            return;
        }

        if (!$user['is_active']) {
            $this->redirectFlash('/login', 'error', 'Your account is deactivated. Contact administrator.');
            return;
        }

        // Rehash if needed (password algorithm upgrade)
        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            $user['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
            UserModel::resetPassword($user['id'], $user['password_hash']);
        }

        // Start session
        Session::regenerate();
        Session::set('user_id', $user['id']);
        Session::set('role', $user['role']);
        Session::set('username', $user['username']);

        // Link to student/teacher
        if ($user['role'] === 'student') {
            $student = StudentModel::getByUser($user['id']);
            Session::set('student_id', $student['id'] ?? null);
        } elseif ($user['role'] === 'teacher') {
            $teacher = Database::fetchOne("SELECT t.* FROM teachers t WHERE t.user_id = ? LIMIT 1", [$user['id']]);
            Session::set('teacher_id', $teacher['id'] ?? null);
        }

        AuditModel::log($user['id'], null, 'login', $this->getIp(), $this->getDevice(), $this->getBrowser());

        $dashboard = match($user['role']) {
            'admin'   => '/admin/dashboard',
            'teacher' => '/teacher/dashboard',
            'student' => '/student/dashboard',
        };

        $this->redirect($dashboard);
    }

    public function logout(): void
    {
        if (Session::get('user_id')) {
            AuditModel::log(Session::get('user_id'), null, 'logout', $this->getIp(), $this->getDevice(), $this->getBrowser());
        }
        Session::destroy();
        $this->redirect('/login');
    }

    public function privacy(): void
    {
        $this->view('auth/privacy', 'auth', [
            'title' => 'Privacy Policy',
        ]);
    }

    public function redirectByRole(): void
    {
        $role = Session::get('role');
        if ($role === 'admin')   { $this->redirect('/admin/dashboard'); }
        elseif ($role === 'teacher') { $this->redirect('/teacher/dashboard'); }
        elseif ($role === 'student') { $this->redirect('/student/dashboard'); }
        else { $this->redirect('/login'); }
    }
}
