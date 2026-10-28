<?php
require_once BASE_PATH . '/app/Core/BaseController.php';
require_once BASE_PATH . '/app/Models/AttendanceModel.php';
require_once BASE_PATH . '/app/Models/StudentModel.php';
require_once BASE_PATH . '/app/Models/ClassModel.php';
require_once BASE_PATH . '/app/Models/AuditModel.php';

class AttendanceController extends BaseController
{
    public function index(): void
    {
        $date = $_GET['date'] ?? null;
        $classCode = $_GET['class'] ?? null;
        $status = $_GET['status'] ?? null;
        $studentId = (int)($_GET['student_id'] ?? 0) ?: null;
        $records = AttendanceModel::getRecords($date, $studentId, $classCode, $status);
        $classes = ClassModel::listAll();
        $students = StudentModel::listAll();

        $this->view('admin/attendance', 'admin', [
            'title' => 'Attendance Records',
            'records' => $records,
            'classes' => $classes, 'students' => $students,
            'date' => $date, 'classCode' => $classCode, 'status' => $status,
            'csrf' => $this->csrfField(),
        ]);
    }

    public function processScan(): void
    {
        if (!self::verifyCsrf()) { $this->jsonError('Invalid CSRF', 403); }

        $studentId = (int)($_POST['student_id'] ?? 0);
        $confidence = (float)($_POST['confidence'] ?? 0);
        $photoData = $_POST['photo'] ?? '';

        if (!$studentId || $confidence < 0) {
            $this->jsonError('Invalid scan data', 422);
        }

        $minConf = (float) SettingsModel::get('face_min_confidence', FACE_MIN_CONFIDENCE);
        if ($confidence < $minConf) {
            $this->jsonError('Face match confidence is too low.', 422);
        }

        $photoPath = null;
        if (!empty($photoData)) {
            $data = preg_replace('/^data:image\/\w+;base64,/', '', $photoData);
            $decoded = base64_decode($data, true);
            if ($decoded !== false) {
                $filename = "att_{$studentId}_" . date('Ymd_His') . '.jpg';
                $fullPath = UPLOAD_DIR . $filename;
                if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
                if (@file_put_contents($fullPath, $decoded)) {
                    $photoPath = $filename;
                }
            }
        }

        $result = AttendanceModel::processScan(
            $studentId, $confidence, $photoPath,
            $this->getIp(), $this->getDevice(), $this->getBrowser(),
            Session::get('user_id')
        );

        $this->json($result + ['student_id' => $studentId]);
    }

    public function studentHistory(string $studentId): void
    {
        $studentId = (int)$studentId;
        $student = StudentModel::findById($studentId);
        if (!$student) { $this->redirect('/admin/students'); }
        $history = AttendanceModel::getStudentHistory($studentId);
        $stats = AttendanceModel::getStats($studentId);
        $this->view('admin/student_attendance', 'admin', [
            'title' => 'Attendance: ' . $student['full_name'],
            'student' => $student,
            'history' => $history, 'stats' => $stats,
            'csrf' => $this->csrfField(),
        ]);
    }
}
