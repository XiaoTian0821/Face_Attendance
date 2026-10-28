<div class="card border-0 shadow-sm">
    <div class="card-header bg-white"><h6 class="mb-0"><i class="fa-solid fa-user me-2"></i>My Profile</h6></div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr><th width="140">Student Code</th><td><strong><?php echo htmlspecialchars($student['student_code']); ?></strong></td></tr>
                    <tr><th>Full Name</th><td><?php echo htmlspecialchars($student['full_name']); ?></td></tr>
                    <tr><th>Email</th><td><?php echo htmlspecialchars($student['email'] ?? '-'); ?></td></tr>
                    <tr><th>Phone</th><td><?php echo htmlspecialchars($student['phone'] ?? '-'); ?></td></tr>
                    <tr><th>Gender</th><td><?php echo htmlspecialchars($student['gender'] ?? '-'); ?></td></tr>
                    <tr><th>Birth Date</th><td><?php echo htmlspecialchars($student['date_of_birth'] ?? '-'); ?></td></tr>
                    <tr><th>Class</th><td><?php echo htmlspecialchars($student['class_name'] ?? 'Unassigned'); ?></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <div class="alert <?php echo $student['face_registered'] ? 'alert-success' : 'alert-warning'; ?>">
                    <?php if ($student['face_registered']): ?>
                        <i class="fa-solid fa-face-id me-2"></i>Face Registered &mdash; Version <?php echo (int)$student['face_registration_version']; ?>
                    <?php else: ?>
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>Face not registered.
                        <a href="<?php echo APP_URL; ?>/student/face">Register Now</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mt-3">
    <div class="card-header bg-white"><h6 class="mb-0"><i class="fa-solid fa-key me-2"></i>Change Password</h6></div>
    <div class="card-body">
        <form id="passwordForm">
            <?php echo $csrf; ?>
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Current Password</label>
                    <input type="password" name="current_password" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">New Password</label>
                    <input type="password" name="new_password" class="form-control form-control-sm" required minlength="8">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control form-control-sm" required>
                </div>
            </div>
            <div class="mt-2 d-flex gap-2 align-items-center">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-check me-1"></i>Update Password</button>
                <span id="passwordMsg" class="small"></span>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('passwordForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const msg = document.getElementById('passwordMsg');
    const fd = new FormData(this);
    const res = await fetch('<?php echo APP_URL; ?>/student/password', {
        method: 'POST',
        body: fd
    });
    const data = await res.json();
    if (data.success) {
        msg.innerHTML = '<span class="text-success">Password updated!</span>';
        this.reset();
    } else {
        msg.innerHTML = '<span class="text-danger">' + (data.message || 'Error') + '</span>';
    }
});
</script>
