<?php
require_once BASE_PATH . '/app/Core/BaseController.php';
require_once BASE_PATH . '/app/Models/TeacherModel.php';
require_once BASE_PATH . '/app/Models/AuditModel.php';
require_once BASE_PATH . '/app/Models/UserModel.php';

class TeacherController extends BaseController
{
    public function index(): void
    {
        $search = $_GET['search'] ?? null;
        $teachers = TeacherModel::listAll($search);
        $this->view('admin/teachers', 'admin', [
            'title' => 'Teachers',
            'teachers' => $teachers, 'search' => $search,
            'csrf' => $this->csrfField(),
        ]);
    }

    public function create(): void
    {
        $users = Database::fetchAll("SELECT id, username, email FROM users WHERE role = 'teacher' AND is_active = 1");
        $this->view('admin/teacher_form', 'admin', [
            'title' => 'Add Teacher',
            'teacher' => null, 'teacherUsers' => $users, 'csrf' => $this->csrfField(),
        ]);
    }

    public function store(): void
    {
        if (!self::verifyCsrf()) { $this->redirectFlash('/admin/teachers', 'error', 'Invalid CSRF'); return; }
        $data = [
            'teacher_code' => trim($_POST['teacher_code'] ?? ''),
            'full_name' => trim($_POST['full_name'] ?? ''),
            'email' => trim($_POST['email'] ?? null) ?: null,
            'phone' => trim($_POST['phone'] ?? null) ?: null,
            'user_id' => (int)($_POST['user_id'] ?? 0) ?: null,
        ];
        if (empty($data['teacher_code']) || empty($data['full_name'])) {
            $this->redirectFlash('/admin/teachers/create', 'error', 'Code and name required.');
            return;
        }
        $id = TeacherModel::create($data);
        AuditModel::log(Session::get('user_id'), null, 'teacher_created',
            $this->getIp(), $this->getDevice(), $this->getBrowser());
        $this->redirectFlash('/admin/teachers', 'success', 'Teacher added.');
    }

    public function show(string $id): void
    {
        $id = (int)$id;
        $teacher = TeacherModel::findById($id);
        if (!$teacher) { $this->redirect('/admin/teachers'); }
        $assignments = TeacherModel::getClassAssignments($id);
        $subjects = TeacherModel::getSubjectAssignments($id);
        $this->view('admin/teacher_detail', 'admin', [
            'title' => 'Teacher: ' . $teacher['full_name'],
            'teacher' => $teacher,
            'assignments' => $assignments, 'subjects' => $subjects,
            'csrf' => $this->csrfField(),
        ]);
    }

    public function edit(string $id): void
    {
        $id = (int)$id;
        $teacher = TeacherModel::findById($id);
        if (!$teacher) { $this->redirect('/admin/teachers'); }
        $users = Database::fetchAll("SELECT id, username, email FROM users WHERE role = 'teacher' AND is_active = 1");
        $this->view('admin/teacher_form', 'admin', [
            'title' => 'Edit Teacher',
            'teacher' => $teacher, 'teacherUsers' => $users, 'csrf' => $this->csrfField(),
        ]);
    }

    public function update(string $id): void
    {
        if (!self::verifyCsrf()) { $this->redirectFlash('/admin/teachers', 'error', 'Invalid CSRF'); return; }
        $id = (int)$id;
        $data = [
            'teacher_code' => trim($_POST['teacher_code'] ?? ''),
            'full_name' => trim($_POST['full_name'] ?? ''),
            'email' => trim($_POST['email'] ?? null) ?: null,
            'phone' => trim($_POST['phone'] ?? null) ?: null,
            'user_id' => (int)($_POST['user_id'] ?? 0) ?: null,
        ];
        if (empty($data['teacher_code']) || empty($data['full_name'])) {
            $this->redirectFlash("/admin/teachers/$id/edit", 'error', 'Code and name required.');
            return;
        }
        TeacherModel::update($id, $data);
        AuditModel::log(Session::get('user_id'), null, 'teacher_updated',
            $this->getIp(), $this->getDevice(), $this->getBrowser());
        $this->redirectFlash('/admin/teachers', 'success', 'Teacher updated.');
    }

    public function destroy(string $id): void
    {
        if (!self::verifyCsrf()) { $this->redirectFlash('/admin/teachers', 'error', 'Invalid CSRF'); return; }
        TeacherModel::delete((int)$id);
        AuditModel::log(Session::get('user_id'), null, 'teacher_deleted',
            $this->getIp(), $this->getDevice(), $this->getBrowser());
        $this->redirectFlash('/admin/teachers', 'success', 'Teacher deleted.');
    }

    public function toggleStatus(string $id): void
    {
        if (!self::verifyCsrf()) { $this->redirectFlash('/admin/teachers', 'error', 'Invalid CSRF'); return; }
        TeacherModel::toggleStatus((int)$id);
        $this->redirectFlash('/admin/teachers', 'success', 'Status updated.');
    }
}
