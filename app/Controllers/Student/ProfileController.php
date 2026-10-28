<?php
require_once BASE_PATH . '/app/Core/BaseController.php';
require_once BASE_PATH . '/app/models/StudentModel.php';
require_once BASE_PATH . '/app/models/UserModel.php';
require_once BASE_PATH . '/app/models/AuditModel.php';

class StudentProfileController extends BaseController
{
    public function index(): void
    {
        $studentId = (int) Session::get('student_id');
        if (!$studentId) { $this->redirect('/login'); }
        $student = StudentModel::findById($studentId);
        if (!$student) { $this->redirect('/login'); }
        $this->view('student/profile', 'student', [
            'title' => 'My Profile',
            'student' => $student, 'csrf' => $this->csrfField(),
        ]);
    }
}
