<?php
/**
 * Route definitions
 */
$router->get('/dashboard', ['middleware' => ['auth'], 'controller' => 'DashboardController', 'action' => 'index']);

// Auth
$router->get('/login', ['middleware' => ['guest'], 'controller' => 'AuthController', 'action' => 'showLogin']);
$router->post('/login', ['controller' => 'AuthController', 'action' => 'login']);
$router->get('/logout', ['middleware' => ['auth'], 'controller' => 'AuthController', 'action' => 'logout']);
$router->get('/privacy', ['middleware' => ['auth'], 'controller' => 'AuthController', 'action' => 'privacy']);

// Admin
$router->get('/admin/dashboard', ['middleware' => ['admin'], 'controller' => 'DashboardController', 'action' => 'admin']);
$router->get('/admin/students', ['middleware' => ['admin'], 'controller' => 'StudentController', 'action' => 'index']);
$router->get('/admin/students/create', ['middleware' => ['admin'], 'controller' => 'StudentController', 'action' => 'create']);
$router->post('/admin/students/store', ['middleware' => ['admin'], 'controller' => 'StudentController', 'action' => 'store']);
$router->get('/admin/students/{id}', ['middleware' => ['admin'], 'controller' => 'StudentController', 'action' => 'show']);
$router->get('/admin/students/{id}/edit', ['middleware' => ['admin'], 'controller' => 'StudentController', 'action' => 'edit']);
$router->post('/admin/students/{id}/update', ['middleware' => ['admin'], 'controller' => 'StudentController', 'action' => 'update']);
$router->post('/admin/students/{id}/delete', ['middleware' => ['admin'], 'controller' => 'StudentController', 'action' => 'destroy']);
$router->post('/admin/students/{id}/toggle-status', ['middleware' => ['admin'], 'controller' => 'StudentController', 'action' => 'toggleStatus']);
$router->get('/admin/students/{id}/face', ['middleware' => ['admin'], 'controller' => 'FaceController', 'action' => 'register']);
$router->post('/admin/students/{id}/face/submit', ['middleware' => ['admin'], 'controller' => 'FaceController', 'action' => 'submit']);

// Admin Teachers
$router->get('/admin/teachers', ['middleware' => ['admin'], 'controller' => 'TeacherController', 'action' => 'index']);
$router->get('/admin/teachers/create', ['middleware' => ['admin'], 'controller' => 'TeacherController', 'action' => 'create']);
$router->post('/admin/teachers/store', ['middleware' => ['admin'], 'controller' => 'TeacherController', 'action' => 'store']);
$router->get('/admin/teachers/{id}', ['middleware' => ['admin'], 'controller' => 'TeacherController', 'action' => 'show']);
$router->get('/admin/teachers/{id}/edit', ['middleware' => ['admin'], 'controller' => 'TeacherController', 'action' => 'edit']);
$router->post('/admin/teachers/{id}/update', ['middleware' => ['admin'], 'controller' => 'TeacherController', 'action' => 'update']);
$router->post('/admin/teachers/{id}/delete', ['middleware' => ['admin'], 'controller' => 'TeacherController', 'action' => 'destroy']);
$router->post('/admin/teachers/{id}/toggle-status', ['middleware' => ['admin'], 'controller' => 'TeacherController', 'action' => 'toggleStatus']);

// Admin Classes
$router->get('/admin/classes', ['middleware' => ['admin'], 'controller' => 'ClassController', 'action' => 'index']);
$router->get('/admin/classes/create', ['middleware' => ['admin'], 'controller' => 'ClassController', 'action' => 'create']);
$router->post('/admin/classes/store', ['middleware' => ['admin'], 'controller' => 'ClassController', 'action' => 'store']);
$router->get('/admin/classes/{id}', ['middleware' => ['admin'], 'controller' => 'ClassController', 'action' => 'show']);
$router->get('/admin/classes/{id}/edit', ['middleware' => ['admin'], 'controller' => 'ClassController', 'action' => 'edit']);
$router->post('/admin/classes/{id}/update', ['middleware' => ['admin'], 'controller' => 'ClassController', 'action' => 'update']);
$router->post('/admin/classes/{id}/delete', ['middleware' => ['admin'], 'controller' => 'ClassController', 'action' => 'destroy']);
$router->post('/admin/classes/{id}/toggle-status', ['middleware' => ['admin'], 'controller' => 'ClassController', 'action' => 'toggleStatus']);

// Admin Subjects
$router->get('/admin/subjects', ['middleware' => ['admin'], 'controller' => 'SubjectController', 'action' => 'index']);
$router->get('/admin/subjects/create', ['middleware' => ['admin'], 'controller' => 'SubjectController', 'action' => 'create']);
$router->post('/admin/subjects/store', ['middleware' => ['admin'], 'controller' => 'SubjectController', 'action' => 'store']);
$router->post('/admin/subjects/{id}/update', ['middleware' => ['admin'], 'controller' => 'SubjectController', 'action' => 'update']);
$router->post('/admin/subjects/{id}/delete', ['middleware' => ['admin'], 'controller' => 'SubjectController', 'action' => 'destroy']);
$router->post('/admin/subjects/{id}/toggle-status', ['middleware' => ['admin'], 'controller' => 'SubjectController', 'action' => 'toggleStatus']);

// Admin Users
$router->get('/admin/users', ['middleware' => ['admin'], 'controller' => 'UserController', 'action' => 'index']);
$router->get('/admin/users/create', ['middleware' => ['admin'], 'controller' => 'UserController', 'action' => 'create']);
$router->post('/admin/users/store', ['middleware' => ['admin'], 'controller' => 'UserController', 'action' => 'store']);
$router->post('/admin/users/{id}/reset-password', ['middleware' => ['admin'], 'controller' => 'UserController', 'action' => 'resetPassword']);
$router->post('/admin/users/{id}/toggle-status', ['middleware' => ['admin'], 'controller' => 'UserController', 'action' => 'toggleStatus']);

// Admin Attendance
$router->get('/admin/attendance', ['middleware' => ['admin'], 'controller' => 'AttendanceController', 'action' => 'index']);
$router->post('/admin/attendance/check', ['middleware' => ['admin'], 'controller' => 'AttendanceController', 'action' => 'processScan']);
$router->get('/admin/attendance/student/{id}', ['middleware' => ['admin'], 'controller' => 'AttendanceController', 'action' => 'studentHistory']);

// Admin Leave
$router->get('/admin/leave', ['middleware' => ['admin'], 'controller' => 'LeaveController', 'action' => 'index']);
$router->post('/admin/leave/{id}/approve', ['middleware' => ['admin'], 'controller' => 'LeaveController', 'action' => 'approve']);
$router->post('/admin/leave/{id}/reject', ['middleware' => ['admin'], 'controller' => 'LeaveController', 'action' => 'reject']);

// Admin Settings
$router->get('/admin/settings', ['middleware' => ['admin'], 'controller' => 'SettingsController', 'action' => 'index']);
$router->post('/admin/settings/update', ['middleware' => ['admin'], 'controller' => 'SettingsController', 'action' => 'update']);

// Admin Audit
$router->get('/admin/audit', ['middleware' => ['admin'], 'controller' => 'AuditController', 'action' => 'index']);

// Admin Export
$router->get('/admin/export/attendance', ['middleware' => ['admin'], 'controller' => 'ExportController', 'action' => 'attendance']);
$router->get('/admin/export/students', ['middleware' => ['admin'], 'controller' => 'ExportController', 'action' => 'students']);

// Kiosk
$router->get('/kiosk', ['middleware' => ['auth'], 'controller' => 'KioskController', 'action' => 'index']);
$router->post('/kiosk/scan', ['middleware' => ['auth'], 'controller' => 'KioskController', 'action' => 'processScan']);

// Teacher
$router->get('/teacher/dashboard', ['middleware' => ['teacher'], 'controller' => 'TeacherDashboardController', 'action' => 'index']);
$router->get('/teacher/attendance', ['middleware' => ['teacher'], 'controller' => 'TeacherAttendanceController', 'action' => 'index']);
$router->get('/teacher/students/{id}', ['middleware' => ['teacher'], 'controller' => 'TeacherStudentController', 'action' => 'attendance']);

// Student
$router->get('/student/dashboard', ['middleware' => ['student'], 'controller' => 'StudentDashboardController', 'action' => 'index']);
$router->get('/student/profile', ['middleware' => ['student'], 'controller' => 'StudentProfileController', 'action' => 'index']);
$router->post('/student/password', ['middleware' => ['student'], 'controller' => 'StudentPasswordController', 'action' => 'update']);
$router->get('/student/face', ['middleware' => ['student'], 'controller' => 'StudentFaceController', 'action' => 'register']);
$router->post('/student/face/submit', ['middleware' => ['student'], 'controller' => 'StudentFaceController', 'action' => 'submit']);
$router->get('/student/attendance', ['middleware' => ['student'], 'controller' => 'StudentAttendanceController', 'action' => 'index']);
$router->get('/student/attendance/stats', ['middleware' => ['student'], 'controller' => 'StudentAttendanceController', 'action' => 'stats']);
$router->post('/student/leave', ['middleware' => ['student'], 'controller' => 'StudentLeaveController', 'action' => 'submit']);
$router->get('/student/leave', ['middleware' => ['student'], 'controller' => 'StudentLeaveController', 'action' => 'index']);

// Root redirect
$router->get('/', ['controller' => 'AuthController', 'action' => 'redirectByRole']);
