<?php
class AttendanceModel
{
    public static function processScan(int $studentId, float $confidence, ?string $photo, string $ip, string $device, string $browser, int $userId): array
    {
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');

        $student = Database::fetchOne("
            SELECT s.*, c.start_time, c.late_grace_minutes, c.rest_start_time, c.rest_end_time, c.id AS class_id
            FROM students s
            LEFT JOIN classes c ON c.id = s.class_id
            WHERE s.id = ? LIMIT 1", [$studentId]);

        if (!$student) {
            return ['success' => false, 'message' => 'Student not found'];
        }

        if (FaceModel::hasApprovedLeave($studentId, $today)) {
            $existing = self::getForDate($studentId, $today);
            if (!$existing) {
                Database::insert('attendance', [
                    'student_id' => $studentId,
                    'attendance_date' => $today,
                    'check_in_time' => $now,
                    'status' => 'on_leave',
                    'check_in_confidence' => $confidence,
                ]);
            }
            AuditModel::log($userId, $studentId, 'attendance_on_leave', $ip, $device, $browser, $confidence);
            return ['success' => true, 'message' => 'Student is on approved leave today.', 'status' => 'on_leave'];
        }

        // Check rest period
        if (self::inRestPeriod($student, $now)) {
            return self::processRestScan($studentId, $confidence, $photo, $student, $now, $ip, $device, $browser, $userId);
        }

        $existing = self::getForDate($studentId, $today);
        $dupInterval = (int) SettingsModel::get('duplicate_scan_interval', 300);

        if ($existing) {
            $lastTime = $existing['check_out_time'] ?: $existing['check_in_time'];
            $elapsed = time() - strtotime($lastTime);
            if ($elapsed < $dupInterval) {
                return ['success' => false, 'message' => 'Attendance already recorded recently. Please wait before scanning again.', 'status' => 'duplicate'];
            }

            $newStatus = ($existing['status'] === 'late') ? 'late' : 'checked_out';
            Database::update('attendance', [
                'check_out_time' => $now,
                'check_out_confidence' => $confidence,
                'check_out_photo' => $photo,
                'status' => $newStatus,
            ], 'id = ?', [$existing['id']]);

            AuditModel::log($userId, $studentId, 'attendance_check_out', $ip, $device, $browser, $confidence, null, ['status' => $newStatus]);
            return ['success' => true, 'message' => 'Check-out successful.', 'status' => $newStatus];
        }

        // First scan - check in
        $lateCutoff = $student['start_time'] ?: '08:00:00';
        $graceMin = (int)($student['late_grace_minutes'] ?? 15);
        $deadline = date('H:i:s', strtotime($lateCutoff) + ($graceMin * 60));
        $currentHms = date('H:i:s');
        $isLate = ($currentHms > $deadline);
        $status = $isLate ? 'late' : 'checked_in';

        Database::insert('attendance', [
            'student_id' => $studentId,
            'attendance_date' => $today,
            'check_in_time' => $now,
            'status' => $status,
            'check_in_confidence' => $confidence,
            'check_in_photo' => $photo,
        ]);

        $msg = $isLate ? 'Late check-in recorded. You arrived after the allowed time.' : 'Check-in successful.';
        AuditModel::log($userId, $studentId, 'attendance_check_in', $ip, $device, $browser, $confidence, null, ['status' => $status]);
        return ['success' => true, 'message' => $msg, 'status' => $status];
    }

    private static function processRestScan(int $studentId, float $confidence, ?string $photo, array $student, string $now, string $ip, string $device, string $browser, int $userId): array
    {
        $today = date('Y-m-d');
        $dupInterval = (int) SettingsModel::get('duplicate_scan_interval', 300);

        $existing = Database::fetchOne("SELECT * FROM rest_attendance WHERE student_id = ? AND rest_date = ? LIMIT 1", [$studentId, $today]);

        if ($existing) {
            $lastTime = $existing['rest_check_out_time'] ?: $existing['rest_check_in_time'];
            $elapsed = time() - strtotime($lastTime);
            if ($elapsed < $dupInterval) {
                return ['success' => false, 'message' => 'Rest attendance already recorded recently.', 'status' => 'duplicate'];
            }
            Database::update('rest_attendance', [
                'rest_check_out_time' => $now,
                'rest_confidence' => $confidence,
                'rest_photo' => $photo,
            ], 'id = ?', [$existing['id']]);
            return ['success' => true, 'message' => 'Rest check-out recorded.', 'status' => 'rest_out'];
        }

        Database::insert('rest_attendance', [
            'student_id' => $studentId,
            'rest_date' => $today,
            'rest_check_in_time' => $now,
            'rest_confidence' => $confidence,
            'rest_photo' => $photo,
            'class_id' => $student['class_id'],
        ]);
        return ['success' => true, 'message' => 'Rest check-in recorded.', 'status' => 'rest_in'];
    }

    private static function inRestPeriod(array $student, string $now): bool
    {
        if (empty($student['rest_start_time']) || empty($student['rest_end_time'])) return false;
        $current = date('H:i:s');
        return ($current >= $student['rest_start_time']) && ($current <= $student['rest_end_time']);
    }

    public static function getForDate(int $studentId, string $date): ?array
    {
        return Database::fetchOne("SELECT * FROM attendance WHERE student_id = ? AND attendance_date = ? LIMIT 1", [$studentId, $date]);
    }

    public static function getStudentHistory(int $studentId, ?string $month = null, ?string $status = null, ?string $date = null): array
    {
        $where = "a.student_id = ?";
        $params = [$studentId];
        if ($month) { $where .= " AND a.attendance_date LIKE ?"; $params[] = "$month%"; }
        if ($date)  { $where = "a.student_id = ? AND a.attendance_date = ?"; $params = [$studentId, $date]; }
        if ($status) { $where .= " AND a.status = ?"; $params[] = $status; }
        return Database::fetchAll("
            SELECT a.*, s.student_code, s.full_name, c.class_code
            FROM attendance a
            JOIN students s ON s.id = a.student_id
            LEFT JOIN classes c ON c.id = s.class_id
            WHERE $where
            ORDER BY a.attendance_date DESC", $params);
    }

    public static function getStats(int $studentId): array
    {
        $total   = (int)(Database::fetchOne("SELECT COUNT(*) AS c FROM attendance WHERE student_id = ?", [$studentId])['c'] ?? 0);
        $present = (int)(Database::fetchOne("SELECT COUNT(*) AS c FROM attendance WHERE student_id = ? AND status IN ('checked_in','checked_out')", [$studentId])['c'] ?? 0);
        $late    = (int)(Database::fetchOne("SELECT COUNT(*) AS c FROM attendance WHERE student_id = ? AND status = 'late'", [$studentId])['c'] ?? 0);
        $leave   = (int)(Database::fetchOne("SELECT COUNT(*) AS c FROM attendance WHERE student_id = ? AND status = 'on_leave'", [$studentId])['c'] ?? 0);
        $pct = $total > 0 ? round(($present / $total) * 100, 1) : 0;
        return ['total' => $total, 'present' => $present, 'late' => $late, 'leave' => $leave, 'percentage' => $pct];
    }

    public static function getRecords(?string $date = null, ?int $studentId = null, ?string $classCode = null, ?string $status = null): array
    {
        $where = "1=1"; $params = [];
        if ($date) { $where .= " AND a.attendance_date = ?"; $params[] = $date; }
        if ($studentId) { $where .= " AND a.student_id = ?"; $params[] = $studentId; }
        if ($classCode) { $where .= " AND c.class_code = ?"; $params[] = $classCode; }
        if ($status) { $where .= " AND a.status = ?"; $params[] = $status; }
        return Database::fetchAll("
            SELECT a.*, s.student_code, s.full_name, c.class_code, c.class_name
            FROM attendance a
            JOIN students s ON s.id = a.student_id
            LEFT JOIN classes c ON c.id = s.class_id
            WHERE $where
            ORDER BY a.attendance_date DESC, a.check_in_time DESC
            LIMIT 500", $params);
    }

    public static function todaySummary(): array
    {
        $today = date('Y-m-d');
        $checkedIn  = (int)(Database::fetchOne("SELECT COUNT(*) AS c FROM attendance WHERE attendance_date = ? AND status IN ('checked_in','late')", [$today])['c'] ?? 0);
        $checkedOut = (int)(Database::fetchOne("SELECT COUNT(*) AS c FROM attendance WHERE attendance_date = ? AND status = 'checked_out'", [$today])['c'] ?? 0);
        $late       = (int)(Database::fetchOne("SELECT COUNT(*) AS c FROM attendance WHERE attendance_date = ? AND status = 'late'", [$today])['c'] ?? 0);
        $onLeave    = (int)(Database::fetchOne("SELECT COUNT(*) AS c FROM attendance WHERE attendance_date = ? AND status = 'on_leave'", [$today])['c'] ?? 0);
        return ['checked_in' => $checkedIn, 'checked_out' => $checkedOut, 'late' => $late, 'on_leave' => $onLeave];
    }
}
