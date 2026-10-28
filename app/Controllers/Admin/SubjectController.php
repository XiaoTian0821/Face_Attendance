<?php
require_once BASE_PATH . '/app/Core/BaseController.php';
require_once BASE_PATH . '/app/Models/SubjectModel.php';
require_once BASE_PATH . '/app/Models/AuditModel.php';

class SubjectController extends BaseController
{
    public function index(): void
    {
        $subjects = SubjectModel::listAll();
        $this->view('admin/subjects', 'admin', [
            'title' => 'Subjects',
            'subjects' => $subjects, 'csrf' => $this->csrfField(),
        ]);
    }

    public function create(): void
    {
        $this->view('admin/subject_form', 'admin', [
            'title' => 'Add Subject',
            'subject' => null, 'csrf' => $this->csrfField(),
        ]);
    }

    public function store(): void
    {
        if (!self::verifyCsrf()) { $this->redirectFlash('/admin/subjects', 'error', 'Invalid CSRF'); return; }
        $data = [
            'subject_code' => trim($_POST['subject_code'] ?? ''),
            'subject_name' => trim($_POST['subject_name'] ?? ''),
            'description' => trim($_POST['description'] ?? null) ?: null,
        ];
        if (empty($data['subject_code']) || empty($data['subject_name'])) {
            $this->redirectFlash('/admin/subjects/create', 'error', 'Code and name required.');
            return;
        }
        $id = SubjectModel::create($data);
        AuditModel::log(Session::get('user_id'), null, 'subject_created',
            $this->getIp(), $this->getDevice(), $this->getBrowser());
        $this->redirectFlash('/admin/subjects', 'success', 'Subject added.');
    }

    public function update(string $id): void
    {
        if (!self::verifyCsrf()) { $this->redirectFlash('/admin/subjects', 'error', 'Invalid CSRF'); return; }
        $id = (int)$id;
        $data = [
            'subject_code' => trim($_POST['subject_code'] ?? ''),
            'subject_name' => trim($_POST['subject_name'] ?? ''),
            'description' => trim($_POST['description'] ?? null) ?: null,
        ];
        if (empty($data['subject_code']) || empty($data['subject_name'])) {
            $this->redirectFlash('/admin/subjects', 'error', 'Code and name required.');
            return;
        }
        SubjectModel::update($id, $data);
        AuditModel::log(Session::get('user_id'), null, 'subject_updated',
            $this->getIp(), $this->getDevice(), $this->getBrowser());
        $this->redirectFlash('/admin/subjects', 'success', 'Subject updated.');
    }

    public function destroy(string $id): void
    {
        if (!self::verifyCsrf()) { $this->redirectFlash('/admin/subjects', 'error', 'Invalid CSRF'); return; }
        SubjectModel::delete((int)$id);
        AuditModel::log(Session::get('user_id'), null, 'subject_deleted',
            $this->getIp(), $this->getDevice(), $this->getBrowser());
        $this->redirectFlash('/admin/subjects', 'success', 'Subject deleted.');
    }

    public function toggleStatus(string $id): void
    {
        if (!self::verifyCsrf()) { $this->redirectFlash('/admin/subjects', 'error', 'Invalid CSRF'); return; }
        SubjectModel::toggleStatus((int)$id);
        $this->redirectFlash('/admin/subjects', 'success', 'Status updated.');
    }
}
