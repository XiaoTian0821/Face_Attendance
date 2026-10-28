<?php
require_once BASE_PATH . '/app/Core/BaseController.php';
require_once BASE_PATH . '/app/models/UserModel.php';
require_once BASE_PATH . '/app/models/StudentModel.php';
require_once BASE_PATH . '/app/models/TeacherModel.php';
require_once BASE_PATH . '/app/models/ClassModel.php';
require_once BASE_PATH . '/app/models/AttendanceModel.php';
require_once BASE_PATH . '/app/models/LeaveModel.php';

class DashboardController extends BaseController
{
    public function index(): void
    {
        $this->redirect('/' . (Session::get('role') ?: 'login') . '/dashboard');
    }

    public function admin(): void
    {
        $this->view('admin/dashboard', 'admin', [
            'title' => 'Admin Dashboard',
            'total_students' => StudentModel::countAll(),
            'active_students' => StudentModel::countActive(),
            'total_teachers' => TeacherModel::countActive(),
            'total_classes' => ClassModel::countAll(),
            'face_registered' => StudentModel::countFaceRegistered(),
            'pending_leaves' => LeaveModel::countPending(),
            'attendance_today' => AttendanceModel::todaySummary(),
            'csrf' => $this->csrfField(),
        ]);
    }
}
