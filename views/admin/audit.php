<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex gap-2">
        <form method="GET" class="d-flex gap-2">
            <input type="text" class="form-control form-control-sm" name="action" value="<?php echo htmlspecialchars($action ?? ''); ?>" placeholder="Filter by action..." style="width:200px">
            <select name="user_id" class="form-select form-select-sm" style="width:auto">
                <option value="">All Users</option>
                <?php foreach ($users as $u): ?>
                <option value="<?php echo $u['id']; ?>" <?php echo $userId == $u['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($u['username']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-filter me-1"></i>Filter</button>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white"><span><?php echo count($logs); ?> log entries (showing latest 100)</span></div>
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Time</th><th>User</th><th>Student</th><th>Action</th><th>IP</th><th>Browser</th><th>Confidence</th><th>Version</th></tr>
            </thead>
            <tbody>
            <?php if (empty($logs)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">No audit logs found.</td></tr>
            <?php endif; ?>
            <?php foreach ($logs as $log): ?>
            <tr>
                <td class="text-nowrap"><?php echo htmlspecialchars($log['created_at']); ?></td>
                <td><?php echo htmlspecialchars($log['user_name'] ?? '—'); ?></td>
                <td><?php echo htmlspecialchars($log['student_code'] ?? '—'); ?></td>
                <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($log['action']); ?></span></td>
                <td class="small text-muted"><?php echo htmlspecialchars($log['ip_address'] ?? ''); ?></td>
                <td class="small"><?php echo htmlspecialchars($log['browser'] ?? '—'); ?></td>
                <td><?php echo $log['confidence'] ? round($log['confidence'], 3) : '—'; ?></td>
                <td><?php echo $log['registration_version'] ? 'v' . (int)$log['registration_version'] : '—'; ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
