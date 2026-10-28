<?php
require_once BASE_PATH . '/app/Core/BaseController.php';
require_once BASE_PATH . '/app/Models/FaceModel.php';
require_once BASE_PATH . '/app/Models/AttendanceModel.php';
require_once BASE_PATH . '/app/Models/SettingsModel.php';
require_once BASE_PATH . '/app/Models/AuditModel.php';

class KioskController extends BaseController
{
    public function index(): void
    {
        $settings = SettingsModel::getFaceSettings();
        $descRows = FaceModel::getActiveDescriptors();

        $studentsForMatching = [];
        foreach ($descRows as $row) {
            if (!$row['student_active']) continue;
            $desc = json_decode($row['descriptor_json'], true);
            if (!is_array($desc)) continue;
            $studentsForMatching[] = [
                'id' => (int)$row['student_id'],
                'student_code' => $row['student_code'],
                'full_name' => $row['full_name'],
                'class_code' => $row['class_code'],
                'class_name' => $row['class_name'],
                'start_time' => $row['start_time'],
                'late_grace_minutes' => $row['late_grace_minutes'],
                'descriptor' => array_map('floatval', $desc),
            ];
        }

        $this->view('kiosk/kiosk', 'kiosk', [
            'title' => 'Attendance Kiosk',
            'settings' => $settings,
            'studentsForMatching' => $studentsForMatching,
            'studentsJson' => json_encode($studentsForMatching),
            'settingsJson' => json_encode($settings),
            'csrf' => $this->csrfField(),
        ]);
    }

    public function processScan(): void
    {
        if (!self::verifyCsrf()) {
            $this->json(['success' => false, 'message' => 'Invalid CSRF token'], 403);
        }

        $studentId = (int)($_POST['student_id'] ?? 0);
        $confidence = (float)($_POST['confidence'] ?? 0);
        $photoData = $_POST['photo'] ?? '';
        $ip = $this->getIp();
        $device = $this->getDevice();
        $browser = $this->getBrowser();

        $photoPath = null;
        if (!empty($photoData)) {
            $data = preg_replace('/^data:image\/\w+;base64,/', '', $photoData);
            $decoded = base64_decode($data, true);
            if ($decoded !== false) {
                $filename = "kiosk_{$studentId}_" . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.jpg';
                $fullPath = UPLOAD_DIR . $filename;
                if (!is_dir(UPLOAD_DIR)) @mkdir(UPLOAD_DIR, 0755, true);
                if (@file_put_contents($fullPath, $decoded)) {
                    $photoPath = $filename;
                }
            }
        }

        $result = AttendanceModel::processScan($studentId, $confidence, $photoPath, $ip, $device, $browser, Session::get('user_id'));
        $this->json($result);
    }
}
