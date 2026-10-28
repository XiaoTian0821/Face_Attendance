<?php
require_once BASE_PATH . '/app/Core/BaseController.php';
require_once BASE_PATH . '/app/Models/TeacherModel.php';

class TeacherAttendanceController extends BaseController
{
    public function index(): void
    {
        $teacherId = (int) Session::get('teacher_id');
        if (!$teacherId) { $this->redirect('/login'); }

        $classIds = TeacherModel::getClassIds($teacherId);
        $date = $_GET['date'] ?? null;
        $status = $_GET['status'] ?? null;

        $records = [];
        if (!empty($classIds)) {
            $inList = implode(',', array_map('intval', $classIds));
            $where = "s.class_id IN ($inList)";
            $params = [];
            if ($date) { $where .= " AND a.attendance_date = ?"; $params[] = $date; }
            else { $where .= " AND a.attendance_date = ?"; $params[] = date('Y-m-d'); }
            if ($status) { $where .= " AND a.status = ?"; $params[] = $status; }

            $records = Database::fetchAll("
                SELECT a.*, s.student_code, s.full_name, c.class_code
                FROM attendance a
                JOIN students s ON s.id = a.student_id
                LEFT JOIN classes c ON c.id = s.class_id
                WHERE $where
                ORDER BY s.student_code", $params);
        }

        $assignments = TeacherModel::getClassAssignments($teacherId);
        $selectedDate = $date ?: date('Y-m-d');

        $this->view('teacher/attendance', 'teacher', [
            'title' => 'Student Attendance',
            'records' => $records,
            'assignments' => $assignments, 'selectedDate' => $selectedDate,
            'status' => $status,
            'csrf' => $this->csrfField(),
        ]);
    }
}
