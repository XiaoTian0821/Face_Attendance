<?php
require_once BASE_PATH . '/app/Core/BaseController.php';
require_once BASE_PATH . '/app/Models/ClassModel.php';
require_once BASE_PATH . '/app/Models/SubjectModel.php';
require_once BASE_PATH . '/app/Models/StudentModel.php';
require_once BASE_PATH . '/app/Models/AuditModel.php';

class ClassController extends BaseController
{
    public function index(): void
    {
        $classes = ClassModel::listAll();
        $this->view('admin/classes', 'admin', [
            'title' => 'Classes',
            'classes' => $classes, 'csrf' => $this->csrfField(),
        ]);
    }

    public function create(): void
    {
        $subjects = SubjectModel::listAll();
        $this->view('admin/class_form', 'admin', [
            'title' => 'Add Class',
            'class' => null, 'subjects' => $subjects, 'csrf' => $this->csrfField(),
        ]);
    }

    public function store(): void
    {
        if (!self::verifyCsrf()) { $this->redirectFlash('/admin/classes', 'error', 'Invalid CSRF'); return; }
        $data = [
            'class_code' => trim($_POST['class_code'] ?? ''),
            'class_name' => trim($_POST['class_name'] ?? ''),
            'start_time' => trim($_POST['start_time'] ?? '08:00') . ':00',
            'late_grace_minutes' => (int)($_POST['late_grace_minutes'] ?? 15),
            'rest_start_time' => !empty($_POST['rest_start_time']) ? trim($_POST['rest_start_time']) . ':00' : null,
            'rest_end_time' => !empty($_POST['rest_end_time']) ? trim($_POST['rest_end_time']) . ':00' : null,
        ];
        if (empty($data['class_code']) || empty($data['class_name'])) {
            $this->redirectFlash('/admin/classes/create', 'error', 'Code and name required.');
            return;
        }
        $id = ClassModel::create($data);
        $subjectIds = array_filter(array_map('intval', $_POST['subject_ids'] ?? []));
        if (!empty($subjectIds)) ClassModel::assignSubjects($id, $subjectIds);
        AuditModel::log(Session::get('user_id'), null, 'class_created',
            $this->getIp(), $this->getDevice(), $this->getBrowser(), null, null, ['class_code' => $data['class_code']]);
        $this->redirectFlash('/admin/classes', 'success', 'Class added.');
    }

    public function show(string $id): void
    {
        $id = (int)$id;
        $class = ClassModel::findById($id);
        if (!$class) { $this->redirect('/admin/classes'); }
        $subjects = ClassModel::getSubjects($id);
        $students = StudentModel::listAll(null, null, $id);
        $this->view('admin/class_detail', 'admin', [
            'title' => 'Class: ' . $class['class_name'],
            'class' => $class, 'subjects' => $subjects,
            'students' => $students, 'allSubjects' => SubjectModel::listAll(),
            'csrf' => $this->csrfField(),
        ]);
    }

    public function edit(string $id): void
    {
        $id = (int)$id;
        $class = ClassModel::findById($id);
        if (!$class) { $this->redirect('/admin/classes'); }
        $subjects = SubjectModel::listAll();
        $assignedSubjects = ClassModel::getSubjects($id);
        $this->view('admin/class_form', 'admin', [
            'title' => 'Edit Class',
            'class' => $class,
            'subjects' => $subjects, 'assignedSubjects' => $assignedSubjects,
            'csrf' => $this->csrfField(),
        ]);
    }

    public function update(string $id): void
    {
        if (!self::verifyCsrf()) { $this->redirectFlash('/admin/classes', 'error', 'Invalid CSRF'); return; }
        $id = (int)$id;
        $data = [
            'class_code' => trim($_POST['class_code'] ?? ''),
            'class_name' => trim($_POST['class_name'] ?? ''),
            'start_time' => trim($_POST['start_time'] ?? '08:00') . ':00',
            'late_grace_minutes' => (int)($_POST['late_grace_minutes'] ?? 15),
            'rest_start_time' => !empty($_POST['rest_start_time']) ? trim($_POST['rest_start_time']) . ':00' : null,
            'rest_end_time' => !empty($_POST['rest_end_time']) ? trim($_POST['rest_end_time']) . ':00' : null,
        ];
        if (empty($data['class_code']) || empty($data['class_name'])) {
            $this->redirectFlash("/admin/classes/$id/edit", 'error', 'Code and name required.');
            return;
        }
        ClassModel::update($id, $data);
        $subjectIds = array_filter(array_map('intval', $_POST['subject_ids'] ?? []));
        ClassModel::assignSubjects($id, $subjectIds);
        AuditModel::log(Session::get('user_id'), null, 'class_updated',
            $this->getIp(), $this->getDevice(), $this->getBrowser());
        $this->redirectFlash('/admin/classes', 'success', 'Class updated.');
    }

    public function destroy(string $id): void
    {
        if (!self::verifyCsrf()) { $this->redirectFlash('/admin/classes', 'error', 'Invalid CSRF'); return; }
        ClassModel::delete((int)$id);
        AuditModel::log(Session::get('user_id'), null, 'class_deleted',
            $this->getIp(), $this->getDevice(), $this->getBrowser());
        $this->redirectFlash('/admin/classes', 'success', 'Class deleted.');
    }

    public function toggleStatus(string $id): void
    {
        if (!self::verifyCsrf()) { $this->redirectFlash('/admin/classes', 'error', 'Invalid CSRF'); return; }
        ClassModel::toggleStatus((int)$id);
        $this->redirectFlash('/admin/classes', 'success', 'Status updated.');
    }
}
