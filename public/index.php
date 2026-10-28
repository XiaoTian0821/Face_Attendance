<?php
/**
 * Front Controller
 */

// Simple PSR-style autoloader
spl_autoload_register(function ($class) {
    $map = [
        // Core
        'Database'       => BASE_PATH . '/app/Core/Database.php',
        'Router'         => BASE_PATH . '/app/Core/Router.php',
        'Session'        => BASE_PATH . '/app/Core/Session.php',
        'BaseController' => BASE_PATH . '/app/Core/BaseController.php',
        // Models
        'UserModel'       => BASE_PATH . '/app/models/UserModel.php',
        'StudentModel'    => BASE_PATH . '/app/models/StudentModel.php',
        'TeacherModel'    => BASE_PATH . '/app/models/TeacherModel.php',
        'ClassModel'      => BASE_PATH . '/app/models/ClassModel.php',
        'SubjectModel'    => BASE_PATH . '/app/models/SubjectModel.php',
        'FaceModel'       => BASE_PATH . '/app/models/FaceModel.php',
        'AttendanceModel' => BASE_PATH . '/app/models/AttendanceModel.php',
        'LeaveModel'      => BASE_PATH . '/app/models/LeaveModel.php',
        'SettingsModel'   => BASE_PATH . '/app/models/SettingsModel.php',
        'AuditModel'      => BASE_PATH . '/app/models/AuditModel.php',
        // Controllers
        'AuthController'          => BASE_PATH . '/app/Controllers/AuthController.php',
        'DashboardController'     => BASE_PATH . '/app/Controllers/Admin/DashboardController.php',
        'StudentController'       => BASE_PATH . '/app/Controllers/Admin/StudentController.php',
        'TeacherController'       => BASE_PATH . '/app/Controllers/Admin/TeacherController.php',
        'ClassController'         => BASE_PATH . '/app/Controllers/Admin/ClassController.php',
        'SubjectController'       => BASE_PATH . '/app/Controllers/Admin/SubjectController.php',
        'UserController'          => BASE_PATH . '/app/Controllers/Admin/UserController.php',
        'FaceController'          => BASE_PATH . '/app/Controllers/Admin/FaceController.php',
        'AttendanceController'    => BASE_PATH . '/app/Controllers/Admin/AttendanceController.php',
        'LeaveController'         => BASE_PATH . '/app/Controllers/Admin/LeaveController.php',
        'SettingsController'      => BASE_PATH . '/app/Controllers/Admin/SettingsController.php',
        'AuditController'         => BASE_PATH . '/app/Controllers/Admin/AuditController.php',
        'ExportController'        => BASE_PATH . '/app/Controllers/Admin/ExportController.php',
        'KioskController'         => BASE_PATH . '/app/Controllers/KioskController.php',
        'TeacherDashboardController'  => BASE_PATH . '/app/Controllers/Teacher/DashboardController.php',
        'TeacherAttendanceController' => BASE_PATH . '/app/Controllers/Teacher/AttendanceController.php',
        'TeacherStudentController'    => BASE_PATH . '/app/Controllers/Teacher/StudentController.php',
        'StudentDashboardController'  => BASE_PATH . '/app/Controllers/Student/DashboardController.php',
        'StudentProfileController'    => BASE_PATH . '/app/Controllers/Student/ProfileController.php',
        'StudentFaceController'       => BASE_PATH . '/app/Controllers/Student/FaceController.php',
        'StudentAttendanceController' => BASE_PATH . '/app/Controllers/Student/AttendanceController.php',
        'StudentLeaveController'      => BASE_PATH . '/app/Controllers/Student/LeaveController.php',
        'StudentPasswordController'   => BASE_PATH . '/app/Controllers/Student/PasswordController.php',
    ];

    if (isset($map[$class]) && file_exists($map[$class])) {
        require $map[$class];
    }
});

// Load config
require __DIR__ . '/../config/config.php';

// Start session
Session::start();

// Generate CSRF token if not present
if (empty(Session::get('csrf_token'))) {
    Session::set('csrf_token', bin2hex(random_bytes(32)));
}

// Determine request path
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$baseDir = parse_url(APP_URL, PHP_URL_PATH) ?? '';
if ($baseDir && strpos($requestUri, $baseDir) === 0) {
    $requestUri = substr($requestUri, strlen($baseDir));
}
$requestUri = $requestUri ?: '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$requestUri = parse_url($requestUri, PHP_URL_PATH) ?: $requestUri;
$requestUri = preg_replace('#^/index\.php(?:/|$)#', '/', $requestUri);
$requestUri = rtrim($requestUri, '/') ?: '/';

// Initialize router
$router = new Router();

// Define routes
require __DIR__ . '/../config/routes.php';

$router->dispatch($method, $requestUri);
