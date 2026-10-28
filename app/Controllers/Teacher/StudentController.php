<?php
require_once BASE_PATH . '/app/Core/BaseController.php';
require_once BASE_PATH . '/app/Models/TeacherModel.php';
require_once BASE_PATH . '/app/Models/StudentModel.php';
require_once BASE_PATH . '/app/Models/AttendanceModel.php';

class TeacherStudentController extends BaseController
{
    public function attendance(string $studentId): void
    {
        $teacherId = (int) Session::get('teacher_id');
        if (!$teacherId) { $this->redirect('/login'); }

        $student = StudentModel::findById((int)$studentId);
        if (!$student) { $this->redirect('/teacher/attendance'); }

        // Verify teacher has access to this student's class
        $classIds = TeacherModel::getClassIds($teacherId);
        if ($student['class_id'] && !in_array((int)$student['class_id'], $classIds)) {
            http_response_code(403);
            header('Content-Type: text/html');
            echo '<!DOCTYPE html><html><head><title>403</title></head><body style="text-align:center;padding:50px">
                <h1>403</h1><p>Access denied</p><a href="' . APP_URL . '/teacher/attendance">Back</a></body></html>';
            return;
        }

        $history = AttendanceModel::getStudentHistory((int)$studentId);
        $stats = AttendanceModel::getStats((int)$studentId);

        $this->view('teacher/student_attendance', 'teacher', [
            'title' => 'Attendance: ' . $student['full_name'],
            'student' => $student,
            'history' => $history, 'stats' => $stats,
            'csrf' => $this->csrfField(),
        ]);
    }
}
