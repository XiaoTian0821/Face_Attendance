<?php
class TeacherModel
{
    public static function findById(int $id): ?array
    {
        return Database::fetchOne("
            SELECT t.*, u.username AS login_username, u.email AS login_email
            FROM teachers t
            LEFT JOIN users u ON u.id = t.user_id
            WHERE t.id = ? LIMIT 1", [$id]);
    }

    public static function findByCode(string $code): ?array
    {
        return Database::fetchOne("SELECT * FROM teachers WHERE teacher_code = ? LIMIT 1", [$code]);
    }

    public static function listAll(?string $search = null): array
    {
        $where = $search ? "WHERE (t.teacher_code LIKE ? OR t.full_name LIKE ?)" : '';
        $params = $search ? ["%$search%", "%$search%"] : [];
        return Database::fetchAll("
            SELECT t.*, u.username AS login_username,
                   (SELECT COUNT(*) FROM teacher_assignments WHERE teacher_id = t.id) AS assignment_count
            FROM teachers t
            LEFT JOIN users u ON u.id = t.user_id
            $where ORDER BY t.teacher_code", $params);
    }

    public static function create(array $data): int
    {
        return Database::insert('teachers', [
            'user_id' => $data['user_id'] ?? null,
            'teacher_code' => $data['teacher_code'],
            'full_name' => $data['full_name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'is_active' => 1,
        ]);
    }

    public static function update(int $id, array $data): void
    {
        $allowed = ['teacher_code','full_name','email','phone','user_id','is_active'];
        $updates = array_intersect_key($data, array_flip($allowed));
        if (!empty($updates)) Database::update('teachers', $updates, 'id = ?', [$id]);
    }

    public static function delete(int $id): void
    {
        Database::delete('teachers', 'id = ?', [$id]);
    }

    public static function toggleStatus(int $id): bool
    {
        $t = self::findById($id);
        if (!$t) return false;
        Database::update('teachers', ['is_active' => $t['is_active'] ? 0 : 1], 'id = ?', [$id]);
        return true;
    }

    public static function countActive(): int
    {
        $r = Database::fetchOne("SELECT COUNT(*) AS cnt FROM teachers WHERE is_active = 1");
        return (int)($r['cnt'] ?? 0);
    }

    public static function getClassAssignments(int $teacherId): array
    {
        return Database::fetchAll("
            SELECT ta.*, c.class_code, c.class_name, s.subject_code, s.subject_name
            FROM teacher_assignments ta
            JOIN classes c ON c.id = ta.class_id
            LEFT JOIN subjects s ON s.id = ta.subject_id
            WHERE ta.teacher_id = ?
            ORDER BY c.class_code, s.subject_code", [$teacherId]);
    }

    public static function getSubjectAssignments(int $teacherId): array
    {
        return Database::fetchAll("
            SELECT DISTINCT s.*
            FROM subjects s
            JOIN teacher_assignments ta ON ta.subject_id = s.id
            WHERE ta.teacher_id = ? AND s.is_active = 1
            ORDER BY s.subject_code", [$teacherId]);
    }

    public static function getClassIds(int $teacherId): array
    {
        $rows = Database::fetchAll("SELECT DISTINCT class_id FROM teacher_assignments WHERE teacher_id = ?", [$teacherId]);
        return array_column($rows, 'class_id');
    }

    /**
     * Add a teacher assignment
     */
    public static function assign(int $teacherId, int $classId, ?int $subjectId): void
    {
        try {
            Database::insert('teacher_assignments', [
                'teacher_id' => $teacherId,
                'class_id' => $classId,
                'subject_id' => $subjectId,
            ]);
        } catch (Exception $e) {
            // duplicate - ignore
        }
    }
}
