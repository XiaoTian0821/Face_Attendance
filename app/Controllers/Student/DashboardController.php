<?php
require_once BASE_PATH . '/app/Core/BaseController.php';
require_once BASE_PATH . '/app/Models/StudentModel.php';
require_once BASE_PATH . '/app/Models/AttendanceModel.php';
require_once BASE_PATH . '/app/Models/LeaveModel.php';

class StudentDashboardController extends BaseController
{
    public function index(): void
    {
        $studentId = (int) Session::get('student_id');
        if (!$studentId) { $this->redirect('/login'); }

        $student = StudentModel::findById($studentId);
        if (!$student) { $this->redirect('/login'); }

        $today = date('Y-m-d');
        $todayRecord = AttendanceModel::getForDate($studentId, $today);
        $stats = AttendanceModel::getStats($studentId);
        $leaves = LeaveModel::findByStudent($studentId);
        $myLeaves = array_slice($leaves, 0, 5);

        $this->view('student/dashboard', 'student', [
            'title' => 'Student Dashboard',
            'student' => $student,
            'todayRecord' => $todayRecord, 'stats' => $stats,
            'myLeaves' => $myLeaves, 'csrf' => $this->csrfField(),
        ]);
    }
}
