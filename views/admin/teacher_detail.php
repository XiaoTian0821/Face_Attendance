<div class="row g-3">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0"><i class="fa-solid fa-user-tie me-2"></i>Teacher Information</h6></div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th width="140">Teacher Code</th><td><?php echo htmlspecialchars($teacher['teacher_code']); ?></td></tr>
                    <tr><th>Full Name</th><td><?php echo htmlspecialchars($teacher['full_name']); ?></td></tr>
                    <tr><th>Email</th><td><?php echo htmlspecialchars($teacher['email'] ?? '-'); ?></td></tr>
                    <tr><th>Phone</th><td><?php echo htmlspecialchars($teacher['phone'] ?? '-'); ?></td></tr>
                    <tr><th>Login User</th><td><?php echo htmlspecialchars($teacher['login_username'] ?? 'Not linked'); ?></td></tr>
                    <tr><th>Status</th><td><span class="badge <?php echo $teacher['is_active'] ? 'bg-success' : 'bg-danger'; ?>"><?php echo $teacher['is_active'] ? 'Active' : 'Inactive'; ?></span></td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white"><h6 class="mb-0">Class Assignments</h6></div>
            <div class="card-body">
                <?php if (empty($assignments)): ?>
                    <p class="text-muted mb-0">No class assignments yet.</p>
                <?php else: ?>
                <table class="table table-sm mb-0">
                    <thead><tr><th>Class</th><th>Subject</th></tr></thead>
                    <tbody>
                    <?php foreach ($assignments as $a): ?>
                        <tr><td><?php echo htmlspecialchars($a['class_name']); ?></td><td><?php echo htmlspecialchars($a['subject_name'] ?? 'All'); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0">Subjects</h6></div>
            <div class="card-body">
                <?php if (empty($subjects)): ?>
                    <p class="text-muted mb-0">No subjects assigned.</p>
                <?php else: ?>
                    <?php foreach ($subjects as $sub): ?>
                        <span class="badge bg-info me-1"><?php echo htmlspecialchars($sub['subject_name']); ?></span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
