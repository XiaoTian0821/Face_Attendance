CREATE DATABASE IF NOT EXISTS face_attendance
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE face_attendance;

DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(60) NOT NULL UNIQUE,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin','teacher','student') NOT NULL DEFAULT 'student',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_users_role (role),
    INDEX idx_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS classes;
CREATE TABLE classes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    class_code VARCHAR(40) NOT NULL UNIQUE,
    class_name VARCHAR(120) NOT NULL,
    start_time TIME NOT NULL DEFAULT '08:00:00',
    late_grace_minutes INT UNSIGNED NOT NULL DEFAULT 15,
    rest_start_time TIME NULL,
    rest_end_time TIME NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_classes_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS subjects;
CREATE TABLE subjects (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subject_code VARCHAR(40) NOT NULL UNIQUE,
    subject_name VARCHAR(120) NOT NULL,
    description TEXT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_subjects_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS teachers;
CREATE TABLE teachers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL UNIQUE,
    teacher_code VARCHAR(40) NOT NULL UNIQUE,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(120) NULL,
    phone VARCHAR(30) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_teachers_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS students;
CREATE TABLE students (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL UNIQUE,
    student_code VARCHAR(40) NOT NULL UNIQUE,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(120) NULL,
    phone VARCHAR(30) NULL,
    gender ENUM('male','female','other') NULL,
    date_of_birth DATE NULL,
    class_id INT UNSIGNED NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    face_registered TINYINT(1) NOT NULL DEFAULT 0,
    face_registration_version INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_students_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_students_class FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_students_class (class_id),
    INDEX idx_students_active (is_active),
    INDEX idx_students_face_reg (face_registered)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS class_subjects;
CREATE TABLE class_subjects (
    class_id INT UNSIGNED NOT NULL,
    subject_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (class_id, subject_id),
    CONSTRAINT fk_class_sub_class FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_class_sub_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS teacher_assignments;
CREATE TABLE teacher_assignments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT UNSIGNED NOT NULL,
    class_id INT UNSIGNED NOT NULL,
    subject_id INT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_assignment (teacher_id, class_id, subject_id),
    CONSTRAINT fk_ta_teacher FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_ta_class FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_ta_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_ta_teacher (teacher_id),
    INDEX idx_ta_class (class_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS face_registrations;
CREATE TABLE face_registrations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    registration_version INT UNSIGNED NOT NULL DEFAULT 1,
    sample_count INT UNSIGNED NOT NULL DEFAULT 1,
    preview_image VARCHAR(255) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    registered_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    registered_by INT UNSIGNED NULL,
    UNIQUE KEY uq_student_version (student_id, registration_version),
    CONSTRAINT fk_fr_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_fr_reg_by FOREIGN KEY (registered_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_fr_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS face_descriptors;
CREATE TABLE face_descriptors (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    registration_id INT UNSIGNED NOT NULL,
    sample_index TINYINT UNSIGNED NOT NULL DEFAULT 1,
    descriptor_json TEXT NOT NULL,          -- JSON array of 128 floats
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fd_fk FOREIGN KEY (registration_id) REFERENCES face_registrations(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX fd_idx_reg (registration_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS attendance;
CREATE TABLE attendance (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    attendance_date DATE NOT NULL,
    check_in_time DATETIME NOT NULL,
    check_out_time DATETIME NULL,
    check_in_confidence FLOAT NULL,
    check_out_confidence FLOAT NULL,
    check_in_photo VARCHAR(255) NULL,
    check_out_photo VARCHAR(255) NULL,
    status ENUM('checked_in','checked_out','late','absent','on_leave') NOT NULL DEFAULT 'checked_in',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_att_date_student (student_id, attendance_date),
    CONSTRAINT fk_att_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_att_date (attendance_date),
    INDEX idx_att_student (student_id),
    INDEX idx_att_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS rest_attendance;
CREATE TABLE rest_attendance (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    rest_date DATE NOT NULL,
    rest_check_in_time DATETIME NOT NULL,
    rest_check_out_time DATETIME NULL,
    rest_confidence FLOAT NULL,
    rest_photo VARCHAR(255) NULL,
    class_id INT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_rest_date_student (student_id, rest_date),
    CONSTRAINT fk_rest_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_rest_class FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_rest_date (rest_date),
    INDEX idx_rest_student (student_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS student_leave_applications;
CREATE TABLE student_leave_applications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    leave_date DATE NOT NULL,
    reason TEXT NOT NULL,
    document_path VARCHAR(255) NULL,
    document_name VARCHAR(255) NULL,
    status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    review_note TEXT NULL,
    reviewed_by INT UNSIGNED NULL,
    reviewed_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_leave_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_leave_reviewer FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_leave_status (status),
    INDEX idx_leave_date (leave_date),
    INDEX idx_leave_student (student_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS audit_logs;
CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    student_id INT UNSIGNED NULL,
    action VARCHAR(120) NOT NULL,
    ip_address VARCHAR(45) NULL,
    device VARCHAR(255) NULL,
    browser VARCHAR(100) NULL,
    confidence FLOAT NULL,
    registration_version INT UNSIGNED NULL,
    metadata JSON NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_audit_user (user_id),
    INDEX idx_audit_action (action),
    INDEX idx_audit_student (student_id),
    INDEX idx_audit_time (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS settings;
CREATE TABLE settings (
    setting_key VARCHAR(120) NOT NULL PRIMARY KEY,
    setting_value TEXT NOT NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO settings (setting_key, setting_value) VALUES
('face_match_threshold',   '0.55'),
('face_min_confidence',    '0.45'),
('face_min_face_size',     '80'),
('face_max_face_size',     '420'),
('face_min_brightness',    '40'),
('face_max_brightness',    '220'),
('face_min_sharpness',     '20'),
('face_detection_confidence','0.50'),
('face_stable_frames',     '3'),
('registration_samples',   '3'),
('duplicate_scan_interval','300'),
('app_name',               'Face Attendance');

INSERT INTO users (username, email, password_hash, role, is_active) VALUES
('admin',    'admin@school.edu', '$2a$12$nHmHulcLaXSwGrNIy/6OmOSeWr/w80Jnqj7GLdvhrWHexbb8xoBly', 'admin',   1),
('teacher1', 't1@school.edu',    '$2a$12$G2Jo2Pv7X909OtYalwJeOuMeZUD8JNWDSSNe1o7Nl4y3R7eZJpXfm', 'teacher', 1),
('student1', 's1@school.edu',    '$2a$12$S2REOCK5XF3oLC4ij2w67OsrIgpwo3lMNwWjKxgUXKzNbTIu9iAOm', 'student', 1),
('student2', 's2@school.edu',    '$2a$12$S2REOCK5XF3oLC4ij2w67OsrIgpwo3lMNwWjKxgUXKzNbTIu9iAOm', 'student', 1);

INSERT INTO classes (class_code, class_name, start_time, late_grace_minutes, rest_start_time, rest_end_time) VALUES
('CS1A', 'Computer Science Year 1 A', '08:00:00', 15, '12:00:00', '13:00:00'),
('CS1B', 'Computer Science Year 1 B', '08:00:00', 15, '12:00:00', '13:00:00'),
('MATH2', 'Mathematics Year 2',        '09:00:00', 10, '12:30:00', '13:30:00');

INSERT INTO subjects (subject_code, subject_name, description, is_active) VALUES
('INTRO', 'Introduction to Programming', 'Fundamentals of programming in Python', 1),
('DBSYS', 'Database Systems',            'SQL, relational databases, and normalisation', 1),
('ALG1',  'Algorithms I',               'Sorting, searching, and complexity analysis', 1),
('MATH101','Mathematics 101',            'Foundations of algebra and geometry', 1);

INSERT INTO class_subjects (class_id, subject_id) VALUES
(1, 1), (1, 2),
(1, 3),
(2, 1),
(2, 4),
(3, 4);

INSERT INTO teachers (user_id, teacher_code, full_name, email, phone, is_active) VALUES
(2, 'TCH-001', 'Alice Teacher',  't1@school.edu', '111-222-333', 1);

INSERT INTO teacher_assignments (teacher_id, class_id, subject_id) VALUES
(1, 1, 1),
(1, 1, 2),
(1, 3, 4);

INSERT INTO students (user_id, student_code, full_name, email, phone, gender, date_of_birth, class_id, is_active, face_registered) VALUES
(3, 'STU-001', 'Bob Student',   's1@school.edu', '444-555-666', 'male',   '2004-05-15', 1, 1, 0),
(4, 'STU-002', 'Carol Student', 's2@school.edu', '777-888-999', 'female', '2005-03-22', 1, 1, 0);

-- Demo attendance sample
INSERT INTO attendance (student_id, attendance_date, check_in_time, status, check_in_confidence) VALUES
(1, CURDATE(), NOW(), 'checked_in', 0.72);
