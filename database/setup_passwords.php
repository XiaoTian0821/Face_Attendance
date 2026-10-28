<?php
/**
 * run this from CLI to set up demo password hashes
 * Usage: php database/setup_passwords.php
 */

$hashes = [
    'admin'    => password_hash('admin123', PASSWORD_DEFAULT),
    'teacher1' => password_hash('teacher123', PASSWORD_DEFAULT),
    'student1' => password_hash('student123', PASSWORD_DEFAULT),
    'student2' => password_hash('student123', PASSWORD_DEFAULT),
];

// Get database connection
$dsn = "mysql:host=localhost;dbname=face_attendance;charset=utf8mb4";
try {
    $pdo = new PDO($dsn, 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (Exception $e) {
    die("DB connection failed: " . $e->getMessage() . "\n");
}

$stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE username = ?");
foreach ($hashes as $username => $hash) {
    $stmt->execute([$hash, $username]);
    echo "Updated password for: $username\n";
}

echo "\nAll demo passwords set successfully!\n";
echo "  admin    / admin123\n";
echo "  teacher1 / teacher123\n";
echo "  student1 / student123\n";
echo "  student2 / student123\n";
