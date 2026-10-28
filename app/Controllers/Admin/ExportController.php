<?php
require_once BASE_PATH . '/app/Core/BaseController.php';
require_once BASE_PATH . '/app/Models/AttendanceModel.php';
require_once BASE_PATH . '/app/Models/StudentModel.php';

class ExportController extends BaseController
{
    public function attendance(): void
    {
        $date = $_GET['date'] ?? null;
        $classCode = $_GET['class'] ?? null;
        $status = $_GET['status'] ?? null;
        $format = $_GET['format'] ?? 'csv';

        $records = AttendanceModel::getRecords($date, null, $classCode, $status);

        if ($format === 'xlsx' || $format === 'excel') {
            $this->exportExcel($records, "attendance_" . date('Ymd') . ".xls");
        } else {
            $this->exportCsv($records, "attendance_" . date('Ymd') . ".csv");
        }
    }

    public function students(): void
    {
        $search = $_GET['search'] ?? null;
        $students = StudentModel::listAll($search);
        $this->exportCsv($students, "students_" . date('Ymd') . ".csv", true);
    }

    private function exportCsv(array $rows, string $filename, bool $isStudents = false): void
    {
        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $out = fopen('php://output', 'w');

        if ($isStudents) {
            fputcsv($out, ['ID','Student Code','Full Name','Email','Phone','Gender','DOB','Class Code','Status','Face Registered']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r['id'],$r['student_code'],$r['full_name'],$r['email']??'',$r['phone']??'',
                    $r['gender']??'',$r['date_of_birth']??'',$r['class_code']??'',
                    $r['is_active']?'Active':'Inactive',$r['face_registered']?'Yes':'No']);
            }
        } else {
            fputcsv($out, ['Date','Student','Student Code','Class','Check In','Check Out','Confidence','Status']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r['attendance_date'],$r['full_name'],$r['student_code'],$r['class_code']??'',
                    $r['check_in_time'],$r['check_out_time']??'',$r['check_in_confidence']??'',$r['status']]);
            }
        }
        fclose($out);
        exit;
    }

    private function exportExcel(array $rows, string $filename): void
    {
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel">';
        echo '<head><meta charset="UTF-8"></head><body><table border="1">';
        echo '<tr>' . implode('', array_map(fn($h) => "<th>" . htmlspecialchars($h) . "</th>",
            ['Date','Student','Student Code','Class','Check In','Check Out','Confidence','Status'])) . '</tr>';
        foreach ($rows as $r) {
            echo '<tr>'
                . '<td>' . htmlspecialchars($r['attendance_date']) . '</td>'
                . '<td>' . htmlspecialchars($r['full_name']) . '</td>'
                . '<td>' . htmlspecialchars($r['student_code']) . '</td>'
                . '<td>' . htmlspecialchars($r['class_code'] ?? '') . '</td>'
                . '<td>' . htmlspecialchars($r['check_in_time']) . '</td>'
                . '<td>' . htmlspecialchars($r['check_out_time'] ?? '') . '</td>'
                . '<td>' . htmlspecialchars($r['check_in_confidence'] ?? '') . '</td>'
                . '<td>' . htmlspecialchars($r['status']) . '</td>'
                . '</tr>';
        }
        echo '</table></body></html>';
        exit;
    }
}
