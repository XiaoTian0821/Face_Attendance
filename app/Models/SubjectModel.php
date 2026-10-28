<?php
class SubjectModel
{
    public static function findById(int $id): ?array
    {
        return Database::fetchOne("SELECT * FROM subjects WHERE id = ? LIMIT 1", [$id]);
    }

    public static function listAll(): array
    {
        return Database::fetchAll("
            SELECT s.*,
                (SELECT COUNT(*) FROM class_subjects WHERE subject_id = s.id) AS class_count
            FROM subjects s
            ORDER BY s.subject_code");
    }

    public static function create(array $data): int
    {
        return Database::insert('subjects', [
            'subject_code' => $data['subject_code'],
            'subject_name' => $data['subject_name'],
            'description' => $data['description'] ?? null,
            'is_active' => 1,
        ]);
    }

    public static function update(int $id, array $data): void
    {
        $allowed = ['subject_code','subject_name','description','is_active'];
        $updates = array_intersect_key($data, array_flip($allowed));
        if (!empty($updates)) Database::update('subjects', $updates, 'id = ?', [$id]);
    }

    public static function delete(int $id): void
    {
        Database::delete('subjects', 'id = ?', [$id]);
    }

    public static function toggleStatus(int $id): bool
    {
        $s = self::findById($id);
        if (!$s) return false;
        Database::update('subjects', ['is_active' => $s['is_active'] ? 0 : 1], 'id = ?', [$id]);
        return true;
    }
}
