<?php
require_once BASE_PATH . '/app/Core/BaseController.php';
require_once BASE_PATH . '/app/Models/SettingsModel.php';
require_once BASE_PATH . '/app/Models/AuditModel.php';

class SettingsController extends BaseController
{
    public function index(): void
    {
        $settings = SettingsModel::getFaceSettings();
        $this->view('admin/settings', 'admin', [
            'title' => 'System Settings',
            'settings' => $settings,
            'csrf' => $this->csrfField(),
        ]);
    }

    public function update(): void
    {
        if (!self::verifyCsrf()) { $this->redirectFlash('/admin/settings', 'error', 'Invalid CSRF'); return; }

        $mapping = [
            'match_threshold' => ['face_match_threshold', 'float'],
            'min_confidence' => ['face_min_confidence', 'float'],
            'min_face_size' => ['face_min_face_size', 'int'],
            'max_face_size' => ['face_max_face_size', 'int'],
            'min_brightness' => ['face_min_brightness', 'int'],
            'max_brightness' => ['face_max_brightness', 'int'],
            'min_sharpness' => ['face_min_sharpness', 'int'],
            'detection_confidence' => ['face_detection_confidence', 'float'],
            'stable_frames' => ['face_stable_frames', 'int'],
            'registration_samples' => ['registration_samples', 'int'],
            'duplicate_scan_interval' => ['duplicate_scan_interval', 'int'],
        ];

        $updates = [];
        foreach ($mapping as $key => [$dbKey, $type]) {
            $val = $_POST[$key] ?? null;
            if ($val !== null && $val !== '') {
                $updates[$dbKey] = $type === 'float' ? (float)$val : (int)$val;
            }
        }

        if (!empty($updates)) {
            SettingsModel::bulkUpdate($updates);
            AuditModel::log(Session::get('user_id'), null, 'settings_changed',
                $this->getIp(), $this->getDevice(), $this->getBrowser(), null, null, $updates);
        }

        $this->redirectFlash('/admin/settings', 'success', 'Settings updated.');
    }
}
