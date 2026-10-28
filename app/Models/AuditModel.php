<?php
class AuditModel
{
    public static function log(?int $userId, ?int $studentId, string $action, ?string $ip = null,
        ?string $device = null, ?string $browser = null, ?float $confidence = null,
        ?int $regVersion = null, ?array $metadata = null): void
    {
        try {
            Database::insert('audit_logs', [
                'user_id' => $userId,
                'student_id' => $studentId,
                'action' => $action,
                'ip_address' => $ip,
                'device' => $device,
                'browser' => $browser,
                'confidence' => $confidence,
                'registration_version' => $regVersion,
                'metadata' => $metadata ? json_encode($metadata) : null,
            ]);
        } catch (Exception $e) {
            // Don't break the flow
        }
    }

    public static function listAll(int $page = 1, int $perPage = 100, ?string $action = null, ?int $userId = null): array
    {
        $where = []; $params = [];
        if ($action) { $where[] = "a.action LIKE ?"; $params[] = "%$action%"; }
        if ($userId) { $where[] = "a.user_id = ?"; $params[] = $userId; }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $offset = ($page - 1) * $perPage;

        return Database::fetchAll("
            SELECT a.*, u.username AS user_name, s.student_code AS student_code
            FROM audit_logs a
            LEFT JOIN users u ON u.id = a.user_id
            LEFT JOIN students s ON s.id = a.student_id
            $whereSql
            ORDER BY a.created_at DESC
            LIMIT $perPage OFFSET $offset", $params);
    }
}
