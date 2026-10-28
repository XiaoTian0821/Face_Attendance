<?php
require_once BASE_PATH . '/app/Core/BaseController.php';
require_once BASE_PATH . '/app/Models/LeaveModel.php';
require_once BASE_PATH . '/app/Models/AuditModel.php';

class LeaveController extends BaseController
{
    public function index(): void
    {
        $status = $_GET['status'] ?? null;
        $leaves = LeaveModel::listAll($status);
        $this->view('admin/leave', 'admin', [
            'title' => 'Leave Applications',
            'leaves' => $leaves, 'status' => $status,
            'csrf' => $this->csrfField(),
        ]);
    }

    public function approve(string $id): void
    {
        if (!self::verifyCsrf()) { $this->redirectFlash('/admin/leave', 'error', 'Invalid CSRF'); return; }
        $id = (int)$id;
        $leave = LeaveModel::findById($id);
        if (!$leave) { $this->redirect('/admin/leave'); }
        $note = trim($_POST['review_note'] ?? '') ?: null;
        LeaveModel::approve($id, Session::get('user_id'), $note ?? '');
        AuditModel::log(Session::get('user_id'), $leave['student_id'], 'leave_approved',
            $this->getIp(), $this->getDevice(), $this->getBrowser(), null, null,
            ['leave_date' => $leave['leave_date']]);
        $this->redirectFlash('/admin/leave', 'success', 'Leave application approved.');
    }

    public function reject(string $id): void
    {
        if (!self::verifyCsrf()) { $this->redirectFlash('/admin/leave', 'error', 'Invalid CSRF'); return; }
        $id = (int)$id;
        $leave = LeaveModel::findById($id);
        if (!$leave) { $this->redirect('/admin/leave'); }
        $note = trim($_POST['review_note'] ?? '') ?: null;
        LeaveModel::reject($id, Session::get('user_id'), $note ?? '');
        AuditModel::log(Session::get('user_id'), $leave['student_id'], 'leave_rejected',
            $this->getIp(), $this->getDevice(), $this->getBrowser(), null, null,
            ['leave_date' => $leave['leave_date']]);
        $this->redirectFlash('/admin/leave', 'success', 'Leave application rejected.');
    }
}
