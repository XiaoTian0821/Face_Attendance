<?php
require_once BASE_PATH . '/app/Core/BaseController.php';
require_once BASE_PATH . '/app/models/StudentModel.php';
require_once BASE_PATH . '/app/models/ClassModel.php';
require_once BASE_PATH . '/app/models/AuditModel.php';
require_once BASE_PATH . '/app/models/FaceModel.php';

class StudentController extends BaseController
{
    public function index(): void
    {
        $search = $_GET['search'] ?? null;
        $activeFilter = isset($_GET['status']) ? ($_GET['status'] === 'active' ? true : ($_GET['status'] === 'inactive' ? false : null)) : null;
        $students = StudentModel::listAll($search, $activeFilter);
        $classes = ClassModel::listAll();
        $this->view('admin/students', 'admin', [
            'title' => 'Students',
            'students' => $students, 'classes' => $classes,
            'search' => $search, 'activeFilter' => $activeFilter,
            'csrf' => $this->csrfField(),
        ]);
    }

    public function create(): void
    {
        $classes = ClassModel::listAll();
        $this->view('admin/student_form', 'admin', [
            'title' => 'Add Student',
            'classes' => $classes, 'student' => null,
            'csrf' => $this->csrfField(),
        ]);
    }

    public function store(): void
    {
        if (!self::verifyCsrf()) { $this->redirectFlash('/admin/students', 'error', 'Invalid CSRF'); return; }
        $data = [
            'student_code' => trim($_POST['student_code'] ?? ''),
            'full_name' => trim($_POST['full_name'] ?? ''),
            'email' => trim($_POST['email'] ?? null) ?: null,
            'phone' => trim($_POST['phone'] ?? null) ?: null,
            'gender' => $_POST['gender'] ?: null,
            'date_of_birth' => $_POST['date_of_birth'] ?: null,
            'class_id' => (int)($_POST['class_id'] ?? 0) ?: null,
        ];
        if (empty($data['student_code']) || empty($data['full_name'])) {
            $this->redirectFlash('/admin/students/create', 'error', 'Student code and name are required.');
            return;
        }
        try {
            $id = StudentModel::create($data);
            AuditModel::log(Session::get('user_id'), $id, 'student_created',
                $this->getIp(), $this->getDevice(), $this->getBrowser());
            $this->redirectFlash('/admin/students', 'success', 'Student added successfully.');
        } catch (Exception $e) {
            $this->redirectFlash('/admin/students/create', 'error', 'Error: ' . $e->getMessage());
        }
    }

    public function show(string $id): void
    {
        $id = (int)$id;
        $student = StudentModel::findById($id);
        if (!$student) { $this->redirect('/admin/students'); }
        $history = FaceModel::getHistory($id);
        $this->view('admin/student_detail', 'admin', [
            'title' => 'Student: ' . $student['full_name'],
            'student' => $student, 'history' => $history,
            'csrf' => $this->csrfField(),
        ]);
    }

    public function edit(string $id): void
    {
        $id = (int)$id;
        $student = StudentModel::findById($id);
        if (!$student) { $this->redirect('/admin/students'); }
        $classes = ClassModel::listAll();
        $this->view('admin/student_form', 'admin', [
            'title' => 'Edit Student',
            'classes' => $classes, 'student' => $student,
            'csrf' => $this->csrfField(),
        ]);
    }

    public function update(string $id): void
    {
        if (!self::verifyCsrf()) { $this->redirectFlash('/admin/students', 'error', 'Invalid CSRF'); return; }
        $id = (int)$id;
        $data = [
            'student_code' => trim($_POST['student_code'] ?? ''),
            'full_name' => trim($_POST['full_name'] ?? ''),
            'email' => trim($_POST['email'] ?? null) ?: null,
            'phone' => trim($_POST['phone'] ?? null) ?: null,
            'gender' => $_POST['gender'] ?: null,
            'date_of_birth' => $_POST['date_of_birth'] ?: null,
            'class_id' => (int)($_POST['class_id'] ?? 0) ?: null,
        ];
        if (empty($data['student_code']) || empty($data['full_name'])) {
            $this->redirectFlash("/admin/students/$id/edit", 'error', 'Code and name required.');
            return;
        }
        StudentModel::update($id, $data);
        AuditModel::log(Session::get('user_id'), $id, 'student_updated',
            $this->getIp(), $this->getDevice(), $this->getBrowser());
        $this->redirectFlash('/admin/students', 'success', 'Student updated.');
    }

    public function destroy(string $id): void
    {
        if (!self::verifyCsrf()) { $this->redirectFlash('/admin/students', 'error', 'Invalid CSRF'); return; }
        $id = (int)$id;
        StudentModel::delete($id);
        AuditModel::log(Session::get('user_id'), $id, 'student_deleted',
            $this->getIp(), $this->getDevice(), $this->getBrowser());
        $this->redirectFlash('/admin/students', 'success', 'Student deleted.');
    }

    public function toggleStatus(string $id): void
    {
        if (!self::verifyCsrf()) { $this->redirectFlash('/admin/students', 'error', 'Invalid CSRF'); return; }
        StudentModel::toggleStatus((int)$id);
        $this->redirectFlash('/admin/students', 'success', 'Status updated.');
    }
}
