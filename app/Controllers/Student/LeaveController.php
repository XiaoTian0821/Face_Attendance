<?php
require_once BASE_PATH . '/app/Core/BaseController.php';
require_once BASE_PATH . '/app/Models/StudentModel.php';
require_once BASE_PATH . '/app/Models/LeaveModel.php';
require_once BASE_PATH . '/app/Models/AuditModel.php';

class StudentLeaveController extends BaseController
{
    public function index(): void
    {
        $studentId = (int) Session::get('student_id');
        if (!$studentId) { $this->redirect('/login'); }
        $student = StudentModel::findById($studentId);
        if (!$student) { $this->redirect('/login'); }
        $leaves = LeaveModel::findByStudent($studentId);
        $this->view('student/leave', 'student', [
            'title' => 'Leave Applications',
            'student' => $student,
            'leaves' => $leaves, 'csrf' => $this->csrfField(),
        ]);
    }

    public function submit(): void
    {
        if (!self::verifyCsrf()) { $this->redirectFlash('/student/leave', 'error', 'Invalid CSRF'); return; }
        $studentId = (int) Session::get('student_id');
        if (!$studentId) { $this->redirect('/login'); }

        $leaveDate = trim($_POST['leave_date'] ?? '');
        $reason = trim($_POST['reason'] ?? '');

        if (empty($leaveDate) || empty($reason)) {
            $this->redirectFlash('/student/leave', 'error', 'Leave date and reason are required.');
            return;
        }

        if (!strtotime($leaveDate)) {
            $this->redirectFlash('/student/leave', 'error', 'Invalid date format.');
            return;
        }

        // Handle file upload
        $docPath = null;
        $docName = null;
        if (!empty($_FILES['document']['name'])) {
            $result = $this->handleUpload($_FILES['document'], $studentId);
            if ($result === false) {
                $this->redirectFlash('/student/leave', 'error', 'Invalid file upload.');
                return;
            }
            [$docPath, $docName] = $result;
        }

        $id = LeaveModel::submit($studentId, $leaveDate, $reason, $docPath, $docName);
        AuditModel::log(Session::get('user_id'), $studentId, 'leave_submitted',
            $this->getIp(), $this->getDevice(), $this->getBrowser(), null, null,
            ['leave_date' => $leaveDate, 'application_id' => $id]);

        $this->redirectFlash('/student/leave', 'success', 'Leave application submitted successfully.');
    }

    private function handleUpload(array $file, int $studentId): array|false
    {
        if ($file['error'] !== UPLOAD_ERR_OK) return false;
        if ($file['size'] > UPLOAD_MAX_SIZE) return false;

        $allowedMimes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'application/pdf' => 'pdf'];
        $finfo = new FInfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        if (!isset($allowedMimes[$mime])) return false;

        $ext = $allowedMimes[$mime];
        $filename = "leave_{$studentId}_" . date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . ".{$ext}";
        $path = UPLOAD_DIR . $filename;

        if (!is_dir(UPLOAD_DIR)) @mkdir(UPLOAD_DIR, 0755, true);
        if (!move_uploaded_file($file['tmp_name'], $path)) return false;

        return [$filename, $file['name']];
    }
}
