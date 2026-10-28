<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex gap-2">
        <form class="d-flex gap-2" method="GET">
            <input type="text" class="form-control form-control-sm" name="action" placeholder="Filter by action..." value="<?php echo htmlspecialchars($action ?? ''); ?>" style="width:180px">
            <button class="btn btn-primary btn-sm" type="submit"><i class="fa-solid fa-filter me-1"></i>Filter</button>
        </form>
    </div>
    <a href="<?php echo APP_URL; ?>/admin/users/create" class="btn btn-primary btn-sm"><i class="fa-solid fa-user-plus me-1"></i>Add User</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Linked</th><th>Status</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($users as $u): ?>
            <tr class="<?php echo !$u['is_active'] ? 'table-secondary' : ''; ?>">
                <td><?php echo (int)$u['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($u['username']); ?></strong></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td><span class="badge bg-<?php echo $u['role'] === 'admin' ? 'danger' : ($u['role'] === 'teacher' ? 'success' : 'info'); ?>"><?php echo strtoupper($u['role']); ?></span></td>
                <td>
                    <?php if ($u['linked_code']): ?>
                        <small><i class="fa-solid fa-user-graduate me-1"></i><?php echo htmlspecialchars($u['linked_code']); ?></small>
                    <?php elseif ($u['teacher_code']): ?>
                        <small><i class="fa-solid fa-user-tie me-1"></i><?php echo htmlspecialchars($u['teacher_code']); ?></small>
                    <?php else: ?>
                        <span class="text-muted">—</span>
                    <?php endif; ?>
                </td>
                <td><span class="badge <?php echo $u['is_active'] ? 'bg-success' : 'bg-danger'; ?>"><?php echo $u['is_active'] ? 'Active' : 'Inactive'; ?></span></td>
                <td class="text-end text-nowrap">
                    <form method="POST" action="<?php echo APP_URL; ?>/admin/users/<?php echo $u['id']; ?>/reset-password" class="d-inline" onsubmit="
                        const pw = prompt('Enter new password (min 8 chars):');
                        if (!pw || pw.length < 8) { alert('Password too short.'); return false; }
                        this.querySelector('[name=new_password]').value = pw;
                        return true;
                    ">
                        <?php echo $csrf; ?>
                        <input type="hidden" name="new_password" value="">
                        <button type="submit" class="btn btn-sm btn-outline-warning" title="Reset Password"><i class="fa-solid fa-key"></i></button>
                    </form>
                    <?php if ((int)$u['id'] !== (int)Session::get('user_id')): ?>
                    <form method="POST" action="<?php echo APP_URL; ?>/admin/users/<?php echo $u['id']; ?>/toggle-status" class="d-inline" onsubmit="return confirm('Toggle user status?')">
                        <?php echo $csrf; ?>
                        <button type="submit" class="btn btn-sm btn-outline-info" title="Toggle"><i class="fa-solid fa-power-off"></i></button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
