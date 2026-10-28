<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="btn-group">
        <a href="<?php echo APP_URL; ?>/admin/leave" class="btn btn-sm <?php echo empty($status) ? 'btn-primary' : 'btn-outline-primary'; ?>">All</a>
        <a href="<?php echo APP_URL; ?>/admin/leave?status=pending" class="btn btn-sm <?php echo $status === 'pending' ? 'btn-primary' : 'btn-outline-primary'; ?>">Pending</a>
        <a href="<?php echo APP_URL; ?>/admin/leave?status=approved" class="btn btn-sm <?php echo $status === 'approved' ? 'btn-primary' : 'btn-outline-primary'; ?>">Approved</a>
        <a href="<?php echo APP_URL; ?>/admin/leave?status=rejected" class="btn btn-sm <?php echo $status === 'rejected' ? 'btn-primary' : 'btn-outline-primary'; ?>">Rejected</a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Student</th><th>Class</th><th>Date</th><th>Reason</th><th>Document</th><th>Status</th><th>Note</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
            <?php if (empty($leaves)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">No leave applications found.</td></tr>
            <?php endif; ?>
            <?php foreach ($leaves as $lv): ?>
            <tr>
                <td>
                    <strong><?php echo htmlspecialchars($lv['full_name']); ?></strong><br>
                    <small class="text-muted"><?php echo htmlspecialchars($lv['student_code']); ?></small>
                </td>
                <td><?php echo htmlspecialchars($lv['class_code'] ?? '—'); ?></td>
                <td><?php echo htmlspecialchars($lv['leave_date']); ?></td>
                <td class="small" style="max-width:180px"><?php echo htmlspecialchars(mb_strimwidth($lv['reason'], 0, 80, '…')); ?></td>
                <td>
                    <?php if ($lv['document_path']): ?>
                        <a href="<?php echo APP_URL; ?>/uploads/<?php echo htmlspecialchars($lv['document_path']); ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-file me-1"></i><?php echo htmlspecialchars($lv['document_name'] ?? 'View'); ?></a>
                    <?php else: ?>
                        <span class="text-muted">—</span>
                    <?php endif; ?>
                </td>
                <td><span class="badge bg-<?php echo $lv['status'] === 'approved' ? 'success' : ($lv['status'] === 'rejected' ? 'danger' : 'warning'); ?>"><?php echo strtoupper($lv['status']); ?></span></td>
                <td class="small text-muted" style="max-width:150px"><?php echo htmlspecialchars($lv['review_note'] ?? ''); ?></td>
                <td class="text-end text-nowrap">
                    <?php if ($lv['status'] === 'pending'): ?>
                    <form method="POST" action="<?php echo APP_URL; ?>/admin/leave/<?php echo $lv['id']; ?>/approve" class="d-inline" onsubmit="return confirm('Approve this leave?')">
                        <?php echo $csrf; ?>
                        <input type="hidden" name="review_note" value="">
                        <button type="submit" class="btn btn-sm btn-success" title="Approve"><i class="fa-solid fa-check"></i></button>
                    </form>
                    <form method="POST" action="<?php echo APP_URL; ?>/admin/leave/<?php echo $lv['id']; ?>/reject" class="d-inline" onsubmit="return confirm('Reject this leave?')">
                        <?php echo $csrf; ?>
                        <input type="hidden" name="review_note" value="">
                        <button type="submit" class="btn btn-sm btn-danger" title="Reject"><i class="fa-solid fa-xmark"></i></button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
