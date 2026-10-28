<?php
require_once BASE_PATH . '/app/Core/BaseController.php';
require_once BASE_PATH . '/app/Models/FaceModel.php';
require_once BASE_PATH . '/app/Models/StudentModel.php';
require_once BASE_PATH . '/app/Models/AuditModel.php';
require_once BASE_PATH . '/app/Models/SettingsModel.php';

class StudentFaceController extends BaseController
{
    public function register(): void
    {
        $studentId = (int) Session::get('student_id');
        if (!$studentId) { $this->redirect('/login'); }

        $student = StudentModel::findById($studentId);
        if (!$student) { $this->redirect('/login'); }

        $settings = SettingsModel::getFaceSettings();
        $registration = FaceModel::getStudentActiveRegistration($studentId);

        $this->view('student/face_register', 'student', [
            'title' => 'Face Registration',
            'student' => $student,
            'settings' => $settings,
            'registration' => $registration,
            'csrf' => $this->csrfField(),
            'settingsJson' => json_encode($settings),
        ]);
    }

    public function submit(): void
    {
        if (!self::verifyCsrf()) { $this->jsonError('Invalid CSRF', 403); }

        $studentId = (int) Session::get('student_id');
        if (!$studentId) { $this->jsonError('No student session', 403); }

        $descriptorsJson = $_POST['descriptors'] ?? '';
        $previewImage = $_POST['preview_image'] ?? '';
        $descriptors = json_decode($descriptorsJson, true);

        if (!is_array($descriptors) || empty($descriptors)) {
            $this->jsonError('No face data received', 422);
        }

        foreach ($descriptors as $d) {
            if (!is_array($d) || count($d) !== 128) {
                $this->jsonError('Invalid descriptor data', 422);
            }
        }

        $previewPath = null;
        if (!empty($previewImage)) {
            $data = preg_replace('/^data:image\/\w+;base64,/', '', $previewImage);
            $decoded = base64_decode($data, true);
            if ($decoded !== false) {
                $filename = "face_{$studentId}_" . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.jpg';
                $fullPath = UPLOAD_DIR . $filename;
                if (!is_dir(UPLOAD_DIR)) @mkdir(UPLOAD_DIR, 0755, true);
                if (@file_put_contents($fullPath, $decoded)) {
                    $previewPath = $filename;
                }
            }
        }

        $regId = FaceModel::register($studentId, $descriptors, $previewPath, Session::get('user_id'));
        $studentAfter = StudentModel::findById($studentId);
        $version = (int)($studentAfter['face_registration_version'] ?? 1);

        AuditModel::log(Session::get('user_id'), $studentId,
            $version > 1 ? 'face_re_registered' : 'face_registered',
            $this->getIp(), $this->getDevice(), $this->getBrowser(),
            null, $version, ['sample_count' => count($descriptors)]);

        $this->json(['success' => true, 'message' => 'Face registration successful.', 'version' => $version]);
    }
}
