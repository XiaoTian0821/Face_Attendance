<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex gap-2">
        <a href="<?php echo APP_URL; ?>/admin/export/attendance?format=csv" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-file-csv me-1"></i>Export CSV</a>
        <a href="<?php echo APP_URL; ?>/admin/export/attendance?format=excel" class="btn btn-outline-success btn-sm"><i class="fa-solid fa-file-excel me-1"></i>Export Excel</a>
    </div>
    <a href="<?php echo APP_URL; ?>/kiosk" class="btn btn-success btn-sm"><i class="fa-solid fa-desktop me-1"></i>Open Kiosk</a>
</div>

<form method="GET" class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small mb-1">Date</label>
                <input type="date" name="date" class="form-control form-control-sm" value="<?php echo htmlspecialchars($date ?? date('Y-m-d')); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Class</label>
                <select name="class" class="form-select form-select-sm">
                    <option value="">All Classes</option>
                    <?php foreach ($classes as $cls): ?>
                    <option value="<?php echo $cls['class_code']; ?>" <?php echo $classCode === $cls['class_code'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cls['class_code']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="checked_in" <?php echo $status === 'checked_in' ? 'selected' : ''; ?>>Checked In</option>
                    <option value="checked_out" <?php echo $status === 'checked_out' ? 'selected' : ''; ?>>Checked Out</option>
                    <option value="late" <?php echo $status === 'late' ? 'selected' : ''; ?>>Late</option>
                    <option value="on_leave" <?php echo $status === 'on_leave' ? 'selected' : ''; ?>>On Leave</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fa-solid fa-filter me-1"></i>Filter</button>
            </div>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white"><span><?php echo count($records); ?> record(s) found</span></div>
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Date</th><th>Student</th><th>Code</th><th>Class</th><th>Check In</th><th>Check Out</th><th>Conf.</th><th>Status</th></tr>
            </thead>
            <tbody>
            <?php if (empty($records)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">No attendance records found.</td></tr>
            <?php endif; ?>
            <?php foreach ($records as $r): ?>
            <tr>
                <td><?php echo htmlspecialchars($r['attendance_date']); ?></td>
                <td><a href="<?php echo APP_URL; ?>/admin/students/<?php echo $r['student_id']; ?>" class="text-decoration-none"><?php echo htmlspecialchars($r['full_name']); ?></a></td>
                <td><?php echo htmlspecialchars($r['student_code']); ?></td>
                <td><?php echo htmlspecialchars($r['class_code'] ?? '—'); ?></td>
                <td><?php echo htmlspecialchars(substr($r['check_in_time'], 11, 5)); ?></td>
                <td><?php echo $r['check_out_time'] ? htmlspecialchars(substr($r['check_out_time'], 11, 5)) : '—'; ?></td>
                <td><?php echo $r['check_in_confidence'] ? round($r['check_in_confidence'], 3) : '—'; ?></td>
                <td>
                    <span class="badge bg-<?php echo match($r['status']) {
                        'late' => 'warning', 'on_leave' => 'info', 'checked_out' => 'secondary', default => 'success'
                    }; ?>"><?php echo strtoupper($r['status']); ?></span>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
