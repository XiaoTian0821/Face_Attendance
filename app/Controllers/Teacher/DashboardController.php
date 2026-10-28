<?php
require_once BASE_PATH . '/app/Core/BaseController.php';
require_once BASE_PATH . '/app/Models/TeacherModel.php';
require_once BASE_PATH . '/app/Models/StudentModel.php';

class TeacherDashboardController extends BaseController
{
    public function index(): void
    {
        $teacherId = (int) Session::get('teacher_id');
        if (!$teacherId) { $this->redirect('/login'); }

        $assignments = TeacherModel::getClassAssignments($teacherId);
        $students = StudentModel::listForTeacher($teacherId);
        $classIds = TeacherModel::getClassIds($teacherId);

        // Today's attendance summary per class
        $todaySummary = [];
        $today = date('Y-m-d');
        foreach ($classIds as $cid) {
            $row = Database::fetchOne("
                SELECT
                    c.class_code, c.class_name,
                    (SELECT COUNT(*) FROM students WHERE class_id = ? AND is_active = 1) AS total,
                    (SELECT COUNT(*) FROM attendance a JOIN students s ON s.id = a.student_id
                     WHERE a.attendance_date = ? AND s.class_id = ? AND a.status IN ('checked_in','late')) AS present
                FROM classes c WHERE c.id = ?", [$cid, $today, $cid, $cid]);
            if ($row) {
                $row['present'] = (int)$row['present'];
                $row['total'] = (int)$row['total'];
                $todaySummary[] = $row;
            }
        }

        $this->view('teacher/dashboard', 'teacher', [
            'title' => 'Teacher Dashboard',
            'teacherId' => $teacherId,
            'assignments' => $assignments,
            'students' => $students,
            'todaySummary' => $todaySummary,
            'csrf' => $this->csrfField(),
        ]);
    }
}
