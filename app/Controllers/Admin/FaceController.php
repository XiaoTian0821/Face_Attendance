<?php
require_once BASE_PATH . '/app/Core/BaseController.php';
require_once BASE_PATH . '/app/Models/FaceModel.php';
require_once BASE_PATH . '/app/Models/StudentModel.php';
require_once BASE_PATH . '/app/Models/AuditModel.php';
require_once BASE_PATH . '/app/Models/SettingsModel.php';

class FaceController extends BaseController
{
    /**
     * Admin face registration page for a student
     */
    public function register(string $studentId): void
    {
        $studentId = (int)$studentId;
        $student = StudentModel::findById($studentId);
        if (!$student) { $this->redirect('/admin/students'); }

        $settings = SettingsModel::getFaceSettings();
        $registration = FaceModel::getStudentActiveRegistration($studentId);

        $this->view('admin/face_register', 'admin', [
            'title' => 'Face Registration: ' . $student['full_name'],
            'student' => $student,
            'settings' => $settings,
            'registration' => $registration,
            'csrf' => $this->csrfField(),
            'settingsJson' => json_encode($settings),
        ]);
    }

    /**
     * Receive descriptors from face registration JS
     */
    public function submit(): void
    {
        if (!self::verifyCsrf()) { $this->jsonError('Invalid CSRF', 403); }

        $studentId = (int)($_POST['student_id'] ?? 0);
        if (!$studentId) { $this->jsonError('Student ID required', 422); }

        $student = StudentModel::findById($studentId);
        if (!$student) { $this->jsonError('Student not found', 404); }

        $descriptorsJson = $_POST['descriptors'] ?? '';
        $previewImage = $_POST['preview_image'] ?? '';

        if (empty($descriptorsJson)) {
            $this->jsonError('No face data received', 422);
        }

        $descriptors = json_decode($descriptorsJson, true);
        if (!is_array($descriptors) || empty($descriptors)) {
            $this->jsonError('Invalid descriptor data', 422);
        }

        foreach ($descriptors as $d) {
            if (!is_array($d) || count($d) !== 128) {
                $this->jsonError('Invalid descriptor dimensions (expected 128)', 422);
            }
        }

        $previewPath = null;
        if (!empty($previewImage)) {
            $previewPath = $this->saveBase64Image($previewImage, "face_reg_{$studentId}");
        }

        $regId = FaceModel::register($studentId, $descriptors, $previewPath, Session::get('user_id'));

        $version = (int)($student['face_registration_version'] + 1);
        AuditModel::log(
            Session::get('user_id'), $studentId,
            $version > 1 ? 'face_re_registered' : 'face_registered',
            $this->getIp(), $this->getDevice(), $this->getBrowser(),
            null, $version,
            ['sample_count' => count($descriptors)]
        );

        $this->json([
            'success' => true,
            'message' => 'Face registration successful.',
            'registration_id' => $regId,
            'version' => $version,
        ]);
    }

    private function saveBase64Image(string $base64, string $prefix): ?string
    {
        $data = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
        $decoded = base64_decode($data, true);
        if ($decoded === false) return null;

        $filename = $prefix . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.jpg';
        $path = UPLOAD_DIR . $filename;

        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }

        if (@file_put_contents($path, $decoded)) {
            return $filename;
        }
        return null;
    }
}
