<?php
class LeaveModel
{
    public static function findByStudent(int $studentId): array
    {
        return Database::fetchAll("
            SELECT l.*, s.student_code, s.full_name
            FROM student_leave_applications l
            JOIN students s ON s.id = l.student_id
            WHERE l.student_id = ?
            ORDER BY l.created_at DESC", [$studentId]);
    }

    public static function listAll(?string $status = null): array
    {
        $where = $status ? "WHERE l.status = ?" : '';
        $params = $status ? [$status] : [];
        return Database::fetchAll("
            SELECT l.*, s.student_code, s.full_name, c.class_code,
                   u.username AS reviewed_by_name
            FROM student_leave_applications l
            JOIN students s ON s.id = l.student_id
            LEFT JOIN classes c ON c.id = s.class_id
            LEFT JOIN users u ON u.id = l.reviewed_by
            $where
            ORDER BY l.created_at DESC
            LIMIT 500", $params);
    }

    public static function findById(int $id): ?array
    {
        return Database::fetchOne("
            SELECT l.*, s.student_code, s.full_name
            FROM student_leave_applications l
            JOIN students s ON s.id = l.student_id
            WHERE l.id = ? LIMIT 1", [$id]);
    }

    public static function submit(int $studentId, string $date, string $reason, ?string $docPath, ?string $docName): int
    {
        return Database::insert('student_leave_applications', [
            'student_id' => $studentId,
            'leave_date' => $date,
            'reason' => $reason,
            'document_path' => $docPath,
            'document_name' => $docName,
            'status' => 'pending',
        ]);
    }

    public static function approve(int $id, int $adminId, string $note = ''): void
    {
        Database::update('student_leave_applications', [
            'status' => 'approved',
            'reviewed_by' => $adminId,
            'reviewed_at' => date('Y-m-d H:i:s'),
            'review_note' => $note ?: null,
        ], 'id = ?', [$id]);
    }

    public static function reject(int $id, int $adminId, string $note = ''): void
    {
        Database::update('student_leave_applications', [
            'status' => 'rejected',
            'reviewed_by' => $adminId,
            'reviewed_at' => date('Y-m-d H:i:s'),
            'review_note' => $note ?: null,
        ], 'id = ?', [$id]);
    }

    public static function countPending(): int
    {
        $r = Database::fetchOne("SELECT COUNT(*) AS c FROM student_leave_applications WHERE status = 'pending'");
        return (int)($r['c'] ?? 0);
    }
}
