<?php
require_once BASE_PATH . '/app/Core/BaseController.php';
require_once BASE_PATH . '/app/Models/StudentModel.php';
require_once BASE_PATH . '/app/Models/AttendanceModel.php';

class StudentAttendanceController extends BaseController
{
    public function index(): void
    {
        $studentId = (int) Session::get('student_id');
        if (!$studentId) { $this->redirect('/login'); }

        $student = StudentModel::findById($studentId);
        if (!$student) { $this->redirect('/login'); }

        $date = $_GET['date'] ?? null;
        $month = $_GET['month'] ?? null;
        $status = $_GET['status'] ?? null;
        $history = AttendanceModel::getStudentHistory($studentId, $month, $status, $date);
        $todayRecord = AttendanceModel::getForDate($studentId, date('Y-m-d'));

        $this->view('student/attendance', 'student', [
            'title' => 'My Attendance',
            'student' => $student,
            'history' => $history, 'todayRecord' => $todayRecord,
            'date' => $date, 'month' => $month, 'status' => $status,
            'csrf' => $this->csrfField(),
        ]);
    }

    public function stats(): void
    {
        $studentId = (int) Session::get('student_id');
        if (!$studentId) { $this->redirect('/login'); }

        $student = StudentModel::findById($studentId);
        if (!$student) { $this->redirect('/login'); }

        $stats = AttendanceModel::getStats($studentId);
        $monthlyData = Database::fetchAll("
            SELECT
                DATE_FORMAT(attendance_date, '%Y-%m') AS month,
                COUNT(*) AS total_days,
                SUM(CASE WHEN status IN ('checked_in','checked_out') THEN 1 ELSE 0 END) AS present_days,
                SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) AS late_days,
                SUM(CASE WHEN status = 'on_leave' THEN 1 ELSE 0 END) AS leave_days
            FROM attendance
            WHERE student_id = ? AND attendance_date >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
            GROUP BY month
            ORDER BY month", [$studentId]);

        $this->view('student/stats', 'student', [
            'title' => 'Attendance Statistics',
            'student' => $student,
            'stats' => $stats, 'monthlyData' => $monthlyData,
            'monthlyDataJson' => json_encode($monthlyData),
            'csrf' => $this->csrfField(),
        ]);
    }
}
