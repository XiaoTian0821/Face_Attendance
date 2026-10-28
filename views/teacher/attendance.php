<?php
// Teacher attendance view - show attendance records for assigned classes
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex gap-2">
        <form method="GET" class="d-flex gap-2">
            <input type="date" name="date" class="form-control form-control-sm" value="<?php echo htmlspecialchars($selectedDate ?? date('Y-m-d')); ?>" required>
            <select name="status" class="form-select form-select-sm" style="width:auto">
                <option value="">All Status</option>
                <option value="checked_in" <?php echo ($status ?? '') === 'checked_in' ? 'selected' : ''; ?>>Checked In</option>
                <option value="checked_out" <?php echo ($status ?? '') === 'checked_out' ? 'selected' : ''; ?>>Checked Out</option>
                <option value="late" <?php echo ($status ?? '') === 'late' ? 'selected' : ''; ?>>Late</option>
                <option value="on_leave" <?php echo ($status ?? '') === 'on_leave' ? 'selected' : ''; ?>>On Leave</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-filter me-1"></i>Filter</button>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span>My Class Assignments</span>
    </div>
    <div class="card-body py-2">
        <?php if (empty($assignments)): ?>
            <p class="text-muted mb-0">No class assignments yet. Contact administrator.</p>
        <?php else: ?>
        <div class="d-flex gap-2 flex-wrap">
            <?php foreach ($assignments as $a): ?>
                <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1 py-1">
                    <i class="fa-solid fa-school"></i>
                    <?php echo htmlspecialchars($a['class_name']); ?>
                    <span class="text-muted">&mdash;</span>
                    <?php echo htmlspecialchars($a['subject_name'] ?? 'All Subjects'); ?>
                </span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white"><span><?php echo count($records); ?> record(s) &mdash; <?php echo htmlspecialchars($selectedDate ?? date('Y-m-d')); ?></span></div>
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Code</th><th>Student</th><th>Class</th><th>Check In</th><th>Check Out</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
            <?php if (empty($records)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">No attendance records for this date.</td></tr>
            <?php endif; ?>
            <?php foreach ($records as $r): ?>
            <tr>
                <td><?php echo htmlspecialchars($r['student_code']); ?></td>
                <td><?php echo htmlspecialchars($r['full_name']); ?></td>
                <td><?php echo htmlspecialchars($r['class_code'] ?? '—'); ?></td>
                <td><?php echo htmlspecialchars(substr($r['check_in_time'], 11, 5)); ?></td>
                <td><?php echo $r['check_out_time'] ? htmlspecialchars(substr($r['check_out_time'], 11, 5)) : '—'; ?></td>
                <td>
                    <span class="badge bg-<?php echo match($r['status']) {
                        'late' => 'warning', 'on_leave' => 'info', 'checked_out' => 'secondary', default => 'success'
                    }; ?>"><?php echo strtoupper($r['status']); ?></span>
                </td>
                <td class="text-end">
                    <a href="<?php echo APP_URL; ?>/teacher/students/<?php echo $r['student_id']; ?>" class="btn btn-sm btn-outline-secondary" title="History">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
