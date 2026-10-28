<div class="d-flex justify-content-between align-items-center mb-3">
    <span></span>
    <a href="<?php echo APP_URL; ?>/admin/subjects/create" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i>Add Subject</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Code</th><th>Subject Name</th><th>Description</th><th>In Classes</th><th>Status</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
            <?php if (empty($subjects)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">No subjects found.</td></tr>
            <?php endif; ?>
            <?php foreach ($subjects as $sub): ?>
            <tr class="<?php echo !$sub['is_active'] ? 'table-secondary' : ''; ?>">
                <td><strong><?php echo htmlspecialchars($sub['subject_code']); ?></strong></td>
                <td><?php echo htmlspecialchars($sub['subject_name']); ?></td>
                <td class="text-muted small"><?php echo htmlspecialchars(mb_strimwidth($sub['description'] ?? '', 0, 60, '…')); ?></td>
                <td><span class="badge bg-light text-dark border"><?php echo (int)$sub['class_count']; ?></span></td>
                <td><span class="badge <?php echo $sub['is_active'] ? 'bg-success' : 'bg-danger'; ?>"><?php echo $sub['is_active'] ? 'Active' : 'Inactive'; ?></span></td>
                <td class="text-end text-nowrap">
                    <form method="POST" action="<?php echo APP_URL; ?>/admin/subjects/<?php echo $sub['id']; ?>/toggle-status" class="d-inline" onsubmit="return confirm('Toggle subject status?')">
                        <?php echo $csrf; ?>
                        <button type="submit" class="btn btn-sm btn-outline-info" title="Toggle"><i class="fa-solid fa-power-off"></i></button>
                    </form>
                    <form method="POST" action="<?php echo APP_URL; ?>/admin/subjects/<?php echo $sub['id']; ?>/delete" class="d-inline" onsubmit="return confirm('Delete this subject? It will be removed from all classes.')">
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
