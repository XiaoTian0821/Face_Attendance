<?php
class FaceModel
{
    public static function register(int $studentId, array $descriptors, ?string $previewImage, int $registeredBy): int
    {
        // Deactivate old registrations
        Database::update('face_registrations', ['is_active' => 0], "student_id = ? AND is_active = 1", [$studentId]);

        // Get next version
        $r = Database::fetchOne("SELECT COALESCE(MAX(registration_version), 0) + 1 AS next_v FROM face_registrations WHERE student_id = ?", [$studentId]);
        $version = (int)($r['next_v'] ?? 1);

        $regId = Database::insert('face_registrations', [
            'student_id' => $studentId,
            'registration_version' => $version,
            'sample_count' => count($descriptors),
            'preview_image' => $previewImage,
            'is_active' => 1,
            'registered_by' => $registeredBy,
        ]);

        $idx = 1;
        foreach ($descriptors as $desc) {
            Database::insert('face_descriptors', [
                'registration_id' => $regId,
                'sample_index' => $idx,
                'descriptor_json' => json_encode($desc),
            ]);
            $idx++;
        }

        Database::update('students', ['face_registered' => 1, 'face_registration_version' => $version], 'id = ?', [$studentId]);
        return $regId;
    }

    public static function getActiveDescriptors(): array
    {
        return Database::fetchAll("
            SELECT s.id AS student_id, s.student_code, s.full_name,
                   s.is_active AS student_active,
                   c.class_code, c.class_name, c.start_time, c.late_grace_minutes,
                   c.rest_start_time, c.rest_end_time,
                   fr.id AS registration_id, fr.registration_version,
                   fd.descriptor_json
            FROM face_descriptors fd
            JOIN face_registrations fr ON fr.id = fd.registration_id
            JOIN students s ON s.id = fr.student_id
            LEFT JOIN classes c ON c.id = s.class_id
            WHERE fr.is_active = 1
            ORDER BY s.student_code");
    }

    public static function getStudentActiveRegistration(int $studentId): ?array
    {
        return Database::fetchOne("
            SELECT fr.*, s.student_code, s.full_name
            FROM face_registrations fr
            JOIN students s ON s.id = fr.student_id
            WHERE fr.student_id = ? AND fr.is_active = 1
            ORDER BY fr.registration_version DESC
            LIMIT 1", [$studentId]);
    }

    public static function hasApprovedLeave(int $studentId, string $date): bool
    {
        $r = Database::fetchOne("
            SELECT COUNT(*) AS cnt FROM student_leave_applications
            WHERE student_id = ? AND leave_date = ? AND status = 'approved'",
            [$studentId, $date]);
        return (int)($r['cnt'] ?? 0) > 0;
    }

    public static function getHistory(int $studentId): array
    {
        return Database::fetchAll("
            SELECT fr.*, u.username AS registered_by_name
            FROM face_registrations fr
            LEFT JOIN users u ON u.id = fr.registered_by
            WHERE fr.student_id = ?
            ORDER BY fr.registration_version DESC", [$studentId]);
    }
}
