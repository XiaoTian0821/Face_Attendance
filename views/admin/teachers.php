<div class="d-flex justify-content-between align-items-center mb-3">
    <form method="GET" class="d-flex gap-2">
        <input type="text" class="form-control form-control-sm" name="search" value="<?php echo htmlspecialchars($search ?? ''); ?>" placeholder="Search teachers...">
        <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-magnifying-glass me-1"></i>Search</button>
    </form>
    <a href="<?php echo APP_URL; ?>/admin/teachers/create" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i>Add Teacher</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Code</th><th>Name</th><th>Email</th><th>Phone</th><th>Login</th><th>Assignments</th><th>Status</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
            <?php if (empty($teachers)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">No teachers found.</td></tr>
            <?php endif; ?>
            <?php foreach ($teachers as $t): ?>
            <tr class="<?php echo !$t['is_active'] ? 'table-secondary' : ''; ?>">
                <td><strong><?php echo htmlspecialchars($t['teacher_code']); ?></strong></td>
                <td><a href="<?php echo APP_URL; ?>/admin/teachers/<?php echo $t['id']; ?>" class="text-decoration-none"><?php echo htmlspecialchars($t['full_name']); ?></a></td>
                <td><?php echo htmlspecialchars($t['email'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($t['phone'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($t['login_username'] ?? 'Not linked'); ?></td>
                <td><span class="badge bg-light text-dark border"><?php echo (int)$t['assignment_count']; ?></span></td>
                <td><span class="badge <?php echo $t['is_active'] ? 'bg-success' : 'bg-danger'; ?>"><?php echo $t['is_active'] ? 'Active' : 'Inactive'; ?></span></td>
                <td class="text-end text-nowrap">
                    <a href="<?php echo APP_URL; ?>/admin/teachers/<?php echo $t['id']; ?>" class="btn btn-sm btn-outline-secondary" title="View"><i class="fa-solid fa-eye"></i></a>
                    <a href="<?php echo APP_URL; ?>/admin/teachers/<?php echo $t['id']; ?>/edit" class="btn btn-sm btn-outline-warning" title="Edit"><i class="fa-solid fa-pen"></i></a>
                    <form method="POST" action="<?php echo APP_URL; ?>/admin/teachers/<?php echo $t['id']; ?>/toggle-status" class="d-inline" onsubmit="return confirm('Toggle teacher status?')">
                        <?php echo $csrf; ?>
                        <button type="submit" class="btn btn-sm btn-outline-info" title="Toggle"><i class="fa-solid fa-power-off"></i></button>
                    </form>
                    <form method="POST" action="<?php echo APP_URL; ?>/admin/teachers/<?php echo $t['id']; ?>/delete" class="d-inline" onsubmit="return confirm('Delete this teacher?')">
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
