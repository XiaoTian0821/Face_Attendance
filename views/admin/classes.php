<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex gap-2">
        <a href="<?php echo APP_URL; ?>/admin/classes" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-list me-1"></i>All Classes</a>
    </div>
    <a href="<?php echo APP_URL; ?>/admin/classes/create" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i>Add Class</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Code</th><th>Name</th><th>Start Time</th><th>Grace (min)</th><th>Rest Period</th><th>Students</th><th>Status</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
            <?php if (empty($classes)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">No classes found.</td></tr>
            <?php endif; ?>
            <?php foreach ($classes as $cls): ?>
            <tr class="<?php echo !$cls['is_active'] ? 'table-secondary' : ''; ?>">
                <td><strong><?php echo htmlspecialchars($cls['class_code']); ?></strong></td>
                <td><a href="<?php echo APP_URL; ?>/admin/classes/<?php echo $cls['id']; ?>" class="text-decoration-none"><?php echo htmlspecialchars($cls['class_name']); ?></a></td>
                <td><?php echo htmlspecialchars(substr($cls['start_time'],0,5)); ?></td>
                <td><?php echo (int)$cls['late_grace_minutes']; ?></td>
                <td><?php echo $cls['rest_start_time'] ? htmlspecialchars(substr($cls['rest_start_time'],0,5) . '–' . substr($cls['rest_end_time'],0,5)) : '—'; ?></td>
                <td><span class="badge bg-light text-dark border"><?php echo (int)$cls['student_count']; ?></span></td>
                <td><span class="badge <?php echo $cls['is_active'] ? 'bg-success' : 'bg-danger'; ?>"><?php echo $cls['is_active'] ? 'Active' : 'Inactive'; ?></span></td>
                <td class="text-end text-nowrap">
                    <a href="<?php echo APP_URL; ?>/admin/classes/<?php echo $cls['id']; ?>" class="btn btn-sm btn-outline-secondary" title="View"><i class="fa-solid fa-eye"></i></a>
                    <a href="<?php echo APP_URL; ?>/admin/classes/<?php echo $cls['id']; ?>/edit" class="btn btn-sm btn-outline-warning" title="Edit"><i class="fa-solid fa-pen"></i></a>
                    <form method="POST" action="<?php echo APP_URL; ?>/admin/classes/<?php echo $cls['id']; ?>/toggle-status" class="d-inline" onsubmit="return confirm('Toggle class status?')">
                        <?php echo $csrf; ?>
                        <button type="submit" class="btn btn-sm btn-outline-info" title="Toggle"><i class="fa-solid fa-power-off"></i></button>
                    </form>
                    <form method="POST" action="<?php echo APP_URL; ?>/admin/classes/<?php echo $cls['id']; ?>/delete" class="d-inline" onsubmit="return confirm('Delete this class? Students will be unassigned.')">
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
