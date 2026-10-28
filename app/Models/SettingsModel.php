<?php
class SettingsModel
{
    private static ?array $cache = null;

    public static function get(string $key, $default = null)
    {
        if (self::$cache === null) {
            $rows = Database::fetchAll("SELECT setting_key, setting_value FROM settings");
            self::$cache = [];
            foreach ($rows as $row) {
                self::$cache[$row['setting_key']] = $row['setting_value'];
            }
        }
        return self::$cache[$key] ?? $default;
    }

    public static function getAll(): array
    {
        if (self::$cache === null) {
            $rows = Database::fetchAll("SELECT setting_key, setting_value FROM settings ORDER BY setting_key");
            self::$cache = [];
            foreach ($rows as $row) {
                self::$cache[$row['setting_key']] = $row['setting_value'];
            }
        }
        return self::$cache;
    }

    public static function set(string $key, string $value): void
    {
        $exists = Database::fetchOne("SELECT 1 AS x FROM settings WHERE setting_key = ?", [$key]);
        if ($exists) {
            Database::update('settings', ['setting_value' => $value], 'setting_key = ?', [$key]);
        } else {
            Database::insert('settings', ['setting_key' => $key, 'setting_value' => $value]);
        }
        self::$cache = null;
    }

    public static function bulkUpdate(array $settings): void
    {
        foreach ($settings as $key => $value) {
            self::set($key, (string)$value);
        }
    }

    public static function getFaceSettings(): array
    {
        return [
            'match_threshold'         => (float) self::get('face_match_threshold', FACE_MATCH_THRESHOLD),
            'min_confidence'          => (float) self::get('face_min_confidence', FACE_MIN_CONFIDENCE),
            'min_face_size'           => (int) self::get('face_min_face_size', FACE_MIN_FACE_SIZE),
            'max_face_size'           => (int) self::get('face_max_face_size', FACE_MAX_FACE_SIZE),
            'min_brightness'          => (int) self::get('face_min_brightness', FACE_MIN_BRIGHTNESS),
            'max_brightness'          => (int) self::get('face_max_brightness', FACE_MAX_BRIGHTNESS),
            'min_sharpness'          => (int) self::get('face_min_sharpness', FACE_MIN_SHARPNESS),
            'detection_confidence'    => (float) self::get('face_detection_confidence', FACE_DETECTION_CONFIDENCE),
            'stable_frames'           => (int) self::get('face_stable_frames', FACE_STABLE_FRAMES),
            'registration_samples'    => (int) self::get('registration_samples', REGISTRATION_SAMPLES),
            'duplicate_scan_interval' => (int) self::get('duplicate_scan_interval', DUPLICATE_SCAN_INTERVAL),
        ];
    }
}
