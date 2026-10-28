<?php
class ClassModel
{
    public static function findById(int $id): ?array
    {
        return Database::fetchOne("SELECT * FROM classes WHERE id = ? LIMIT 1", [$id]);
    }

    public static function listAll(): array
    {
        return Database::fetchAll("
            SELECT c.*,
                (SELECT COUNT(*) FROM students WHERE class_id = c.id AND is_active = 1) AS student_count
            FROM classes c
            ORDER BY c.class_code");
    }

    public static function create(array $data): int
    {
        return Database::insert('classes', [
            'class_code' => $data['class_code'],
            'class_name' => $data['class_name'],
            'start_time' => $data['start_time'] ?? '08:00:00',
            'late_grace_minutes' => $data['late_grace_minutes'] ?? 15,
            'rest_start_time' => $data['rest_start_time'] ?? null,
            'rest_end_time' => $data['rest_end_time'] ?? null,
            'is_active' => 1,
        ]);
    }

    public static function update(int $id, array $data): void
    {
        $allowed = ['class_code','class_name','start_time','late_grace_minutes','rest_start_time','rest_end_time','is_active'];
        $updates = array_intersect_key($data, array_flip($allowed));
        if (!empty($updates)) Database::update('classes', $updates, 'id = ?', [$id]);
    }

    public static function delete(int $id): void
    {
        Database::delete('classes', 'id = ?', [$id]);
    }

    public static function toggleStatus(int $id): bool
    {
        $c = self::findById($id);
        if (!$c) return false;
        Database::update('classes', ['is_active' => $c['is_active'] ? 0 : 1], 'id = ?', [$id]);
        return true;
    }

    public static function countAll(): int
    {
        $r = Database::fetchOne("SELECT COUNT(*) AS cnt FROM classes WHERE is_active = 1");
        return (int)($r['cnt'] ?? 0);
    }

    public static function getSubjects(int $classId): array
    {
        return Database::fetchAll("
            SELECT s.*, cs.id AS rel_id
            FROM subjects s
            JOIN class_subjects cs ON cs.subject_id = s.id
            WHERE cs.class_id = ? AND s.is_active = 1
            ORDER BY s.subject_code", [$classId]);
    }

    public static function assignSubjects(int $classId, array $subjectIds): void
    {
        Database::delete('class_subjects', 'class_id = ?', [$classId]);
        foreach (array_unique($subjectIds) as $sid) {
            if ((int)$sid > 0) {
                Database::insert('class_subjects', ['class_id' => $classId, 'subject_id' => (int)$sid]);
            }
        }
    }
}
