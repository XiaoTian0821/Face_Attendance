<div class="row g-3 mb-4">
    <?php
    $cards = [
        ['Students', 'fa-users', 'total_students', 'primary'],
        ['Active Students', 'fa-user-check', 'active_students', 'success'],
        ['Teachers', 'fa-user-tie', 'total_teachers', 'info'],
        ['Classes', 'fa-school', 'total_classes', 'warning'],
        ['Faces Registered', 'fa-face-id', 'face_registered', 'primary'],
        ['Pending Leaves', 'fa-calendar-minus', 'pending_leaves', 'danger'],
    ];
    foreach ($cards as [$label, $icon, $key, $color]):
    ?>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card stat-card h-100 border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fa-solid <?php echo $icon; ?> text-<?php echo $color; ?> fs-1 mb-2"></i>
                <div class="fs-2 fw-bold"><?php echo htmlspecialchars((string)(${$key} ?? 0)); ?></div>
                <small class="text-muted"><?php echo $label; ?></small>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-3 mb-4">
    <?php
    $att = $attendance_today ?? [];
    $attCards = [
        ['Checked In Today', 'fa-sign-in-alt', $att['checked_in'] ?? 0, 'success'],
        ['Checked Out', 'fa-sign-out-alt', $att['checked_out'] ?? 0, 'info'],
        ['Late Today', 'fa-clock', $att['late'] ?? 0, 'warning'],
        ['On Leave Today', 'fa-calendar-xmark', $att['on_leave'] ?? 0, 'danger'],
    ];
    foreach ($attCards as [$label, $icon, $val, $color]):
    ?>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="fa-solid <?php echo $icon; ?> text-<?php echo $color; ?>"></i>
                <div class="fs-3 fw-bold"><?php echo (int)$val; ?></div>
                <small class="text-muted"><?php echo $label; ?></small>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white"><h6 class="mb-0"><i class="fa-solid fa-bolt me-2"></i>Quick Actions</h6></div>
    <div class="card-body">
        <div class="row g-2">
            <div class="col-6"><a href="<?php echo APP_URL; ?>/admin/students/create" class="btn btn-outline-primary btn-sm w-100"><i class="fa-solid fa-user-plus me-2"></i>Add Student</a></div>
            <div class="col-6"><a href="<?php echo APP_URL; ?>/kiosk" class="btn btn-outline-success btn-sm w-100"><i class="fa-solid fa-desktop me-2"></i>Open Kiosk</a></div>
            <div class="col-6"><a href="<?php echo APP_URL; ?>/admin/export/attendance" class="btn btn-outline-secondary btn-sm w-100"><i class="fa-solid fa-file-csv me-2"></i>Export Attendance</a></div>
            <div class="col-6"><a href="<?php echo APP_URL; ?>/admin/leave" class="btn btn-outline-warning btn-sm w-100"><i class="fa-solid fa-calendar-minus me-2"></i>Review Leaves</a></div>
        </div>
    </div>
</div>
