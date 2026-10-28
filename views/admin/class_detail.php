<div class="row g-3">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fa-solid fa-school me-2"></i>Class Details</h6>
                <a href="<?php echo APP_URL; ?>/admin/classes/<?php echo $class['id']; ?>/edit" class="btn btn-sm btn-outline-warning"><i class="fa-solid fa-pen me-1"></i>Edit</a>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th width="140">Class Code</th><td><?php echo htmlspecialchars($class['class_code']); ?></td></tr>
                    <tr><th>Class Name</th><td><?php echo htmlspecialchars($class['class_name']); ?></td></tr>
                    <tr><th>Start Time</th><td><?php echo htmlspecialchars(substr($class['start_time'], 0, 5)); ?></td></tr>
                    <tr><th>Grace Period</th><td><?php echo (int)$class['late_grace_minutes']; ?> minutes</td></tr>
                    <tr><th>Rest Period</th><td><?php echo $class['rest_start_time'] ? htmlspecialchars(substr($class['rest_start_time'],0,5) . '–' . substr($class['rest_end_time'],0,5)) : 'Not set'; ?></td></tr>
                    <tr><th>Status</th><td><span class="badge <?php echo $class['is_active'] ? 'bg-success' : 'bg-danger'; ?>"><?php echo $class['is_active'] ? 'Active' : 'Inactive'; ?></span></td></tr>
                </table>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0">Subjects</h6></div>
            <div class="card-body">
                <?php if (empty($subjects)): ?>
                    <p class="text-muted mb-0">No subjects assigned.</p>
                <?php else: ?>
                    <?php foreach ($subjects as $sub): ?>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span><span class="text-muted me-2"><?php echo htmlspecialchars($sub['subject_code']); ?></span><?php echo htmlspecialchars($sub['subject_name']); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0">Students in Class</h6></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Code</th><th>Name</th><th>Face</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php if (empty($students)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-3">No students in this class yet.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($students as $s): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($s['student_code']); ?></td>
                            <td><a href="<?php echo APP_URL; ?>/admin/students/<?php echo $s['id']; ?>" class="text-decoration-none"><?php echo htmlspecialchars($s['full_name']); ?></a></td>
                            <td><?php echo $s['face_registered'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>'; ?></td>
                            <td><?php echo $s['is_active'] ? '<span class="badge bg-success-subtle text-success">Active</span>' : '<span class="badge bg-danger-subtle text-danger">Inactive</span>'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
