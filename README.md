# Face Attendance System

A web-based facial recognition student attendance management system built with PHP, MySQL, and face-api.js.

## Project Description

This system allows educational institutions to:
- Manage students, teachers, classes, and subjects
- Register and re-register student faces using the browser camera
- Record attendance via a dedicated kiosk (face scanning)
- Process leave applications
- View attendance records, statistics, and audit logs
- Export attendance and student data (CSV / Excel)

---

## Requirements

- **PHP** 8.0 or higher
- **MySQL** 5.7+ / **MariaDB** 10.3+
- **Apache** with `mod_rewrite` enabled
- **XAMPP** (Windows/Linux) or equivalent
- **Browser**: Chrome, Edge, or Firefox (camera access required)
- **HTTPS** (production) or **localhost** (development)

---

## Installation

### 1. XAMPP Setup

1. Install XAMPP from [apachefriends.org](https://www.apachefriends.org/)
2. Start **Apache** and **MySQL** from the XAMPP Control Panel
3. Open a browser and verify `http://localhost` loads

### 2. Copy the Project

Copy the entire project folder to your XAMPP `htdocs` directory:

```
C:\xampp\htdocs\Face_Attendance
```

### 3. Create the Database

1. Open **phpMyAdmin** at `http://localhost/phpmyadmin`
2. Go to **Import** tab
3. Select `database/install.sql`
4. Click **Go**

This creates:
- Database: `face_attendance`
- All tables (users, students, teachers, classes, subjects, attendance, etc.)
- Demo data (1 admin, 1 teacher, 2 students, 3 classes, 4 subjects)

### 4. Configure Environment

Copy `.env.example` to `.env` and edit:

```
APP_URL=http://localhost/Face_Attendance/public
DB_HOST=localhost
DB_NAME=face_attendance
DB_USER=root
DB_PASS=
```

> **Important**: `DB_PASS` should be empty for XAMPP's default MySQL.

### 5. Set Up Face Models

The application ships with a **face-api.js stub** that provides the API surface
for development and demonstration.

**For production face recognition**, download the real face-api.js and its model files:

1. Download face-api.js: https://github.com/justadudewhohacks/face-api.js
   - Copy `face-api.js` to `public/assets/vendor/face-api.min.js`
2. Download model files from: https://github.com/justadudewhohacks/face-api.js/tree/master/weights
   - Place all files in `public/assets/models/`:
     ```
     tiny_face_detector_model-weights_manifest.json
     tiny_face_detector_model-shard1
     face_landmark_68_model-weights_manifest.json
     face_landmark_68_model-shard1
     face_recognition_model-weights_manifest.json
     face_recognition_model-shard1
     face_recognition_model-shard2
     ```

### 6. Set Up Demo Passwords

The install script uses placeholder password hashes. After first import,
run this in a PHP file or CLI to generate real hashes:

```bash
php -r "echo password_hash('admin123', PASSWORD_DEFAULT), PHP_EOL;"
php -r "echo password_hash('teacher123', PASSWORD_DEFAULT), PHP_EOL;"
php -r "echo password_hash('student123', PASSWORD_DEFAULT), PHP_EOL;"
```

Then update the `users` table:

```sql
USE face_attendance;
UPDATE users SET password_hash = '<admin_hash>' WHERE username = 'admin';
UPDATE users SET password_hash = '<teacher_hash>' WHERE username = 'teacher1';
UPDATE users SET password_hash = '<student_hash>' WHERE username IN ('student1','student2');
```

### 7. Open the Application

Navigate to:

```
http://localhost/Face_Attendance/public/
```

You should see the login page.

---

## Demo Accounts

| Role    | Username | Password      |
|---------|----------|---------------|
| Admin   | `admin`  | `admin123`    |
| Teacher | `teacher1` | `teacher123` |
| Student | `student1` | `student123` |
| Student | `student2` | `student123` |

> **Note**: Update passwords before production use. The default hashes in `install.sql`
> are placeholders — use the password reset flow in the admin panel to set real passwords.

---

## Project Structure

```
Face_Attendance/
├── app/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── KioskController.php
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── StudentController.php
│   │   │   ├── TeacherController.php
│   │   │   ├── ClassController.php
│   │   │   ├── SubjectController.php
│   │   │   ├── UserController.php
│   │   │   ├── FaceController.php
│   │   │   ├── AttendanceController.php
│   │   │   ├── LeaveController.php
│   │   │   ├── SettingsController.php
│   │   │   ├── AuditController.php
│   │   │   └── ExportController.php
│   │   ├── Teacher/
│   │   │   ├── DashboardController.php
│   │   │   ├── AttendanceController.php
│   │   │   └── StudentController.php
│   │   └── Student/
│   │       ├── DashboardController.php
│   │       ├── ProfileController.php
│   │       ├── FaceController.php
│   │       ├── AttendanceController.php
│   │       ├── LeaveController.php
│   │       └── PasswordController.php
│   ├── Core/
│   │   ├── BaseController.php
│   │   ├── Database.php
│   │   ├── Router.php
│   │   └── Session.php
│   └── Models/
│       ├── UserModel.php
│       ├── StudentModel.php
│       ├── TeacherModel.php
│       ├── ClassModel.php
│       ├── SubjectModel.php
│       ├── FaceModel.php
│       ├── AttendanceModel.php
│       ├── LeaveModel.php
│       ├── SettingsModel.php
│       └── AuditModel.php
│
├── config/
│   ├── config.php
│   └── routes.php
│
├── database/
│   └── install.sql
│
├── public/
│   ├── index.php
│   ├── .htaccess
│   ├── assets/
│   │   ├── css/app.css
│   │   ├── js/face/
│   │   │   ├── camera.js
│   │   │   ├── face-engine.js
│   │   │   ├── quality.js
│   │   │   ├── pose.js
│   │   │   ├── matcher.js
│   │   │   ├── registration-flow.js
│   │   │   ├── attendance-flow.js
│   │   │   └── kiosk-flow.js
│   │   ├── models/          ← Place face-api model files here
│   │   └── vendor/face-api.min.js
│   ├── uploads/
│   │   └── .htaccess
│
├── views/
│   ├── layouts/
│   │   ├── admin.php
│   │   ├── teacher.php
│   │   ├── student.php
│   │   ├── kiosk.php
│   │   └── auth.php
│   ├── auth/
│   │   ├── login.php
│   │   └── privacy.php
│   ├── admin/
│   │   ├── dashboard.php
│   │   ├── students.php
│   │   ├── student_form.php
│   │   ├── student_detail.php
│   │   ├── student_attendance.php
│   │   ├── face_register.php
│   │   ├── teachers.php
│   │   ├── teacher_form.php
│   │   ├── teacher_detail.php
│   │   ├── classes.php
│   │   ├── class_form.php
│   │   ├── class_detail.php
│   │   ├── subjects.php
│   │   ├── subject_form.php
│   │   ├── attendance.php
│   │   ├── leave.php
│   │   ├── users.php
│   │   ├── user_form.php
│   │   ├── settings.php
│   │   └── audit.php
│   ├── teacher/
│   │   ├── dashboard.php
│   │   ├── attendance.php
│   │   └── student_attendance.php
│   ├── student/
│   │   ├── dashboard.php
│   │   ├── profile.php
│   │   ├── face_register.php
│   │   ├── attendance.php
│   │   ├── stats.php
│   │   └── leave.php
│   ├── kiosk/
│   │   └── kiosk.php
│   └── errors/
│       ├── 403.php
│       ├── 404.php
│       └── 500.php
│
├── .env
├── .env.example
├── .htaccess
└── README.md
```

---

## Configuration Reference (`.env`)

| Key | Description | Default |
|-----|-------------|---------|
| `APP_URL` | Full URL to the public directory | `http://localhost/Face_Attendance/public` |
| `APP_DEBUG` | Show detailed errors | `true` |
| `DB_HOST` | Database host | `localhost` |
| `DB_NAME` | Database name | `face_attendance` |
| `DB_USER` | DB user | `root` |
| `DB_PASS` | DB password | *(empty)* |
| `SESSION_NAME` | Session cookie name | `face_att_sess` |
| `FACE_MATCH_THRESHOLD` | Distance threshold for matching | `0.55` |
| `FACE_MIN_CONFIDENCE` | Minimum attendance confidence | `0.45` |
| `DUPLICATE_SCAN_INTERVAL` | Seconds between same-student scans | `300` |
| `UPLOAD_MAX_SIZE` | Max upload size in bytes | `5242880` |

> Face settings stored in the database (Admin → Settings) override `.env` defaults at runtime.

---

## Face Recognition Setup

### Model Files Required

The application uses **face-api.js** with three models:

| Model | Purpose |
|-------|---------|
| Tiny Face Detector | Detect face bounding boxes |
| Face Landmark 68 | Detect facial landmarks |
| Face Recognition | Extract 128-dim descriptors for matching |

### Model Directory

Place model files in: `public/assets/models/`

Required files:
```
tiny_face_detector_model-weights_manifest.json
tiny_face_detector_model-shard1
face_landmark_68_model-weights_manifest.json
face_landmark_68_model-shard1
face_recognition_model-weights_manifest.json
face_recognition_model-shard1
face_recognition_model-shard2
```

### Camera Permissions

Browsers require **secure context** for camera access:
- `localhost` (HTTP) — works in all supported browsers
- `https` (production) — required on non-localhost domains

To grant camera access:
1. Navigate to the face registration or kiosk page
2. Click "Start Camera"
3. Allow camera access when prompted

---

## Testing Checklist

### Authentication
- [ ] Admin login with correct credentials
- [ ] Admin login with wrong password (error shown)
- [ ] Teacher login
- [ ] Student login
- [ ] Logout
- [ ] Access admin page as student → 403
- [ ] Access student page as admin → 403

### Student Management
- [ ] View student list
- [ ] Add student
- [ ] Edit student
- [ ] Delete student
- [ ] Toggle student status
- [ ] View student details

### Face Registration
- [ ] Register face (admin)
- [ ] Register face (student self-service)
- [ ] Re-register face (version increments)
- [ ] Face quality feedback (too small, too large, blurry, dark)
- [ ] Multiple face detection warning

### Attendance
- [ ] Kiosk check-in
- [ ] Kiosk check-out (after interval)
- [ ] Late attendance (after grace period)
- [ ] Duplicate scan blocked
- [ ] Rest period attendance
- [ ] Student on approved leave → "on_leave" status

### Leave
- [ ] Submit leave application (with document)
- [ ] Approve leave (admin)
- [ ] Reject leave (admin)

### Administration
- [ ] Manage teachers
- [ ] Manage classes
- [ ] Manage subjects
- [ ] Assign subjects to classes
- [ ] Export attendance CSV
- [ ] Export students CSV
- [ ] Change settings
- [ ] View audit logs

---

## Troubleshooting

### Camera not working
- **Chrome/Edge**: Check `chrome://settings/content/camera` — allow access
- **Firefox**: Check `about:preferences#privacy` — allow camera
- **HTTPS required**: On non-localhost, camera access requires HTTPS
- **No camera on machine**: Use a virtual camera tool or test on a laptop with webcam

### Face models not loading
- Verify files exist in `public/assets/models/`
- Check browser console for 404 errors
- Ensure file permissions allow Apache to read them

### Login fails
- Verify database was imported correctly
- Check that `password_hash` values are valid (use password reset in admin)
- Confirm `DB_USER` and `DB_PASS` in `.env` are correct

### CSRF errors on form submit
- Ensure you're not using multiple tabs
- Refresh the page and try again
- Clear browser cache/cookies

### Duplicate scans not blocked
- Check `duplicate_scan_interval` in Admin → Settings
- Default is 300 seconds (5 minutes)

---

## Privacy Information

This system stores:
- **Face descriptors** (128-dim vectors) — used for matching only
- **Registration/attendance photos** — visual evidence of presence
- **Attendance timestamps** — check-in/check-out times
- **Audit logs** — user actions for security purposes

Access is controlled by role. Facial data is not shared outside the system.
See the **Privacy Policy** link in the sidebar for the full policy.

---

## Security Features

- ✅ PDO prepared statements (all SQL queries)
- ✅ `password_hash()` / `password_verify()` for passwords
- ✅ CSRF tokens on all POST forms
- ✅ Session regeneration after login
- ✅ Role-based access control (admin/teacher/student)
- ✅ Output escaping with `htmlspecialchars()`
- ✅ File upload MIME validation
- ✅ Random filenames for uploads
- ✅ Upload directory PHP execution disabled via `.htaccess`
- ✅ HTTP 403 for unauthorized role access
