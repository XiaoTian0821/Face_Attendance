<?php
class StudentModel
{
    public static function findById(int $id): ?array
    {
        return Database::fetchOne("
            SELECT s.*, c.class_code, c.class_name, c.start_time, c.late_grace_minutes,
                   c.rest_start_time, c.rest_end_time,
                   u.username AS login_username, u.email AS login_email
            FROM students s
            LEFT JOIN classes c ON c.id = s.class_id
            LEFT JOIN users u ON u.id = s.user_id
            WHERE s.id = ? LIMIT 1", [$id]);
    }

    public static function findByCode(string $code): ?array
    {
        return Database::fetchOne("SELECT * FROM students WHERE student_code = ? LIMIT 1", [$code]);
    }

    public static function listAll(?string $search = null, ?bool $active = null, ?int $classId = null): array
    {
        $where = []; $params = [];
        if ($search !== null) {
            $where[] = "(s.student_code LIKE ? OR s.full_name LIKE ? OR s.email LIKE ?)";
            $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
        }
        if ($active !== null) { $where[] = "s.is_active = ?"; $params[] = $active ? 1 : 0; }
        if ($classId !== null) { $where[] = "s.class_id = ?"; $params[] = $classId; }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        return Database::fetchAll("
            SELECT s.*, c.class_code, c.class_name, u.username AS login_username
            FROM students s
            LEFT JOIN classes c ON c.id = s.class_id
            LEFT JOIN users u ON u.id = s.user_id
            $whereSql ORDER BY s.student_code", $params);
    }

    public static function create(array $data): int
    {
        return Database::insert('students', [
            'user_id' => null,
            'student_code' => $data['student_code'],
            'full_name' => $data['full_name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'gender' => $data['gender'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'class_id' => $data['class_id'] ?? null,
            'is_active' => 1,
        ]);
    }

    public static function update(int $id, array $data): void
    {
        $allowed = ['student_code','full_name','email','phone','gender','date_of_birth','class_id','is_active'];
        $updates = array_intersect_key($data, array_flip($allowed));
        if (!empty($updates)) Database::update('students', $updates, 'id = ?', [$id]);
    }

    public static function delete(int $id): void
    {
        Database::delete('students', 'id = ?', [$id]);
    }

    public static function toggleStatus(int $id): bool
    {
        $s = self::findById($id);
        if (!$s) return false;
        Database::update('students', ['is_active' => $s['is_active'] ? 0 : 1], 'id = ?', [$id]);
        return true;
    }

    public static function countActive(): int
    {
        $r = Database::fetchOne("SELECT COUNT(*) AS cnt FROM students WHERE is_active = 1");
        return (int)($r['cnt'] ?? 0);
    }

    public static function countAll(): int
    {
        $r = Database::fetchOne("SELECT COUNT(*) AS cnt FROM students");
        return (int)($r['cnt'] ?? 0);
    }

    public static function countFaceRegistered(): int
    {
        $r = Database::fetchOne("SELECT COUNT(*) AS cnt FROM students WHERE face_registered = 1 AND is_active = 1");
        return (int)($r['cnt'] ?? 0);
    }

    public static function getByUser(int $userId): ?array
    {
        return Database::fetchOne("
            SELECT s.*, c.class_code, c.class_name
            FROM students s
            LEFT JOIN classes c ON c.id = s.class_id
            WHERE s.user_id = ? LIMIT 1", [$userId]);
    }

    public static function listForTeacher(int $teacherId): array
    {
        $classIds = TeacherModel::getClassIds($teacherId);
        if (empty($classIds)) return [];
        $inList = implode(',', array_map('intval', $classIds));
        return Database::fetchAll("
            SELECT DISTINCT s.*, c.class_code, c.class_name
            FROM students s
            JOIN classes c ON c.id = s.class_id
            WHERE c.id IN ($inList) AND s.is_active = 1
            ORDER BY s.student_code");
    }
}
