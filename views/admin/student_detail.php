<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fa-solid fa-user me-2"></i>Student Details</h6>
        <div>
            <a href="<?php echo APP_URL; ?>/admin/students/<?php echo $student['id']; ?>/face" class="btn btn-sm btn-primary"><i class="fa-solid fa-face-id me-1"></i>Register Face</a>
            <a href="<?php echo APP_URL; ?>/admin/students/<?php echo $student['id']; ?>/edit" class="btn btn-sm btn-outline-warning"><i class="fa-solid fa-pen me-1"></i>Edit</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr><th width="140">Student Code</th><td><?php echo htmlspecialchars($student['student_code']); ?></td></tr>
                    <tr><th>Full Name</th><td><?php echo htmlspecialchars($student['full_name']); ?></td></tr>
                    <tr><th>Email</th><td><?php echo htmlspecialchars($student['email'] ?? '-'); ?></td></tr>
                    <tr><th>Phone</th><td><?php echo htmlspecialchars($student['phone'] ?? '-'); ?></td></tr>
                    <tr><th>Gender</th><td><?php echo htmlspecialchars($student['gender'] ?? '-'); ?></td></tr>
                    <tr><th>Date of Birth</th><td><?php echo htmlspecialchars($student['date_of_birth'] ?? '-'); ?></td></tr>
                    <tr><th>Class</th><td><?php echo htmlspecialchars($student['class_name'] ?? 'Unassigned'); ?></td></tr>
                    <tr><th>Status</th><td><span class="badge <?php echo $student['is_active'] ? 'bg-success' : 'bg-danger'; ?>"><?php echo $student['is_active'] ? 'Active' : 'Inactive'; ?></span></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Face Registration</h6>
                <?php if ($student['face_registered']): ?>
                    <div class="alert alert-success">
                        <i class="fa-solid fa-check me-2"></i>Face Registered &mdash; Version <?php echo (int)$student['face_registration_version']; ?>
                    </div>
                    <?php if (!empty($history)): ?>
                    <table class="table table-sm">
                        <thead><tr><th>Version</th><th>Samples</th><th>By</th><th>Date</th></tr></thead>
                        <tbody>
                        <?php foreach ($history as $h): ?>
                            <tr class="<?php echo !$h['is_active'] ? 'table-secondary' : ''; ?>">
                                <td>v<?php echo (int)$h['registration_version']; ?></td>
                                <td><?php echo (int)$h['sample_count']; ?></td>
                                <td><?php echo htmlspecialchars($h['registered_by_name'] ?? 'System'); ?></td>
                                <td><?php echo htmlspecialchars(substr((string)$h['registered_at'], 0, 16)); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>Face not yet registered.
                        <a href="<?php echo APP_URL; ?>/admin/students/<?php echo $student['id']; ?>/face">Register Now</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
