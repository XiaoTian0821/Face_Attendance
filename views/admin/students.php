<div class="d-flex justify-content-between align-items-center mb-3">
    <form class="d-flex gap-2 flex-wrap" method="GET">
        <input type="text" class="form-control form-control-sm" name="search" value="<?php echo htmlspecialchars($search ?? ''); ?>" placeholder="Search code, name, or email...">
        <select name="status" class="form-select form-select-sm" style="width:auto">
            <option value="">All Status</option>
            <option value="active" <?php echo ($activeFilter === true) ? 'selected' : ''; ?>>Active</option>
            <option value="inactive" <?php echo ($activeFilter === false) ? 'selected' : ''; ?>>Inactive</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-magnifying-glass me-1"></i>Search</button>
        <?php if ($search || $activeFilter !== null): ?><a href="<?php echo APP_URL; ?>/admin/students" class="btn btn-outline-secondary btn-sm">Clear</a><?php endif; ?>
    </form>
    <div>
        <a href="<?php echo APP_URL; ?>/admin/export/students" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-download me-1"></i>Export CSV</a>
        <a href="<?php echo APP_URL; ?>/admin/students/create" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i>Add Student</a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Code</th><th>Name</th><th>Email</th><th>Class</th>
                    <th>Face</th><th>Status</th><th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($students)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">No students found.</td></tr>
                <?php endif; ?>
                <?php foreach ($students as $s): ?>
                <tr class="<?php echo !$s['is_active'] ? 'table-secondary' : ''; ?>">
                    <td><strong><?php echo htmlspecialchars($s['student_code']); ?></strong></td>
                    <td>
                        <a href="<?php echo APP_URL; ?>/admin/students/<?php echo $s['id']; ?>" class="text-decoration-none"><?php echo htmlspecialchars($s['full_name']); ?></a>
                        <br><small class="text-muted"><?php echo htmlspecialchars($s['login_username'] ?? 'No login'); ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($s['email'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($s['class_code'] ?? '-'); ?></td>
                    <td>
                        <?php if ($s['face_registered']): ?>
                            <span class="badge bg-success">Registered v<?php echo (int)$s['face_registration_version']; ?></span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Not Registered</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($s['is_active']): ?>
                            <span class="badge bg-success-subtle text-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-danger-subtle text-danger">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end text-nowrap">
                        <a href="<?php echo APP_URL; ?>/admin/students/<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-secondary" title="View"><i class="fa-solid fa-eye"></i></a>
                        <a href="<?php echo APP_URL; ?>/admin/students/<?php echo $s['id']; ?>/face" class="btn btn-sm btn-outline-primary" title="Face"><i class="fa-solid fa-face-id"></i></a>
                        <a href="<?php echo APP_URL; ?>/admin/students/<?php echo $s['id']; ?>/edit" class="btn btn-sm btn-outline-warning" title="Edit"><i class="fa-solid fa-pen"></i></a>
                        <form method="POST" action="<?php echo APP_URL; ?>/admin/students/<?php echo $s['id']; ?>/toggle-status" class="d-inline" onsubmit="return confirm('Toggle status?')">
                            <?php echo $csrf; ?>
                            <button type="submit" class="btn btn-sm btn-outline-info" title="Toggle"><i class="fa-solid fa-power-off"></i></button>
                        </form>
                        <form method="POST" action="<?php echo APP_URL; ?>/admin/students/<?php echo $s['id']; ?>/delete" class="d-inline" onsubmit="return confirm('Delete this student? This cannot be undone.')">
                            <?php echo $csrf; ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
