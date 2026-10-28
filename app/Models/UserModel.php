<?php
class UserModel
{
    public static function findByUsername(string $username): ?array
    {
        return Database::fetchOne("SELECT * FROM users WHERE username = ? LIMIT 1", [$username]);
    }

    public static function findByEmail(string $email): ?array
    {
        return Database::fetchOne("SELECT * FROM users WHERE email = ? LIMIT 1", [$email]);
    }

    public static function findById(int $id): ?array
    {
        return Database::fetchOne("SELECT * FROM users WHERE id = ? LIMIT 1", [$id]);
    }

    public static function create(string $username, string $email, string $passwordHash, string $role): int
    {
        return Database::insert('users', [
            'username' => $username,
            'email' => $email,
            'password_hash' => $passwordHash,
            'role' => $role,
        ]);
    }

    public static function update(int $id, array $data): void
    {
        $allowed = ['username', 'email', 'role', 'is_active'];
        $updates = array_intersect_key($data, array_flip($allowed));
        if (!empty($updates)) {
            Database::update('users', $updates, 'id = ?', [$id]);
        }
    }

    public static function resetPassword(int $id, string $newHash): void
    {
        Database::update('users', ['password_hash' => $newHash], 'id = ?', [$id]);
    }

    public static function toggleStatus(int $id): bool
    {
        $user = self::findById($id);
        if (!$user) return false;
        $newActive = $user['is_active'] ? 0 : 1;
        Database::update('users', ['is_active' => $newActive], 'id = ?', [$id]);
        return true;
    }

    public static function listAll(): array
    {
        return Database::fetchAll("
            SELECT u.*,
                s.student_code AS linked_code,
                s.full_name AS linked_name,
                s.id AS linked_id,
                t.teacher_code AS teacher_code,
                t.full_name AS teacher_full_name
            FROM users u
            LEFT JOIN students s ON s.user_id = u.id
            LEFT JOIN teachers t ON t.user_id = u.id
            ORDER BY u.id DESC
        ");
    }

    public static function countByRole(string $role): int
    {
        $r = Database::fetchOne("SELECT COUNT(*) AS cnt FROM users WHERE role = ? AND is_active = 1", [$role]);
        return (int)($r['cnt'] ?? 0);
    }
}
