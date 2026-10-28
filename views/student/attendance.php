<div class="row g-3 mb-3">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <?php if ($todayRecord): ?>
                    <h5 class="text-success mb-1"><i class="fa-solid fa-check-circle me-2"></i>Today: <?php echo strtoupper($todayRecord['status']); ?></h5>
                    <p class="text-muted mb-0">
                        Checked in at <?php echo htmlspecialchars(substr($todayRecord['check_in_time'], 11, 5)); ?>
                        <?php if ($todayRecord['check_out_time']): ?>
                            &mdash; Checked out at <?php echo htmlspecialchars(substr($todayRecord['check_out_time'], 11, 5)); ?>
                        <?php endif; ?>
                    </p>
                <?php else: ?>
                    <h5 class="text-muted"><i class="fa-solid fa-clock me-2"></i>No attendance recorded today</h5>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<form method="GET" class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Date</label>
                <input type="date" name="date" class="form-control form-control-sm" value="<?php echo htmlspecialchars($date ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Month (YYYY-MM)</label>
                <input type="month" name="month" class="form-control form-control-sm" value="<?php echo htmlspecialchars($month ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="checked_in" <?php echo $status === 'checked_in' ? 'selected' : ''; ?>>Checked In</option>
                    <option value="checked_out" <?php echo $status === 'checked_out' ? 'selected' : ''; ?>>Checked Out</option>
                    <option value="late" <?php echo $status === 'late' ? 'selected' : ''; ?>>Late</option>
                    <option value="on_leave" <?php echo $status === 'on_leave' ? 'selected' : ''; ?>>On Leave</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fa-solid fa-filter me-1"></i>Filter</button>
            </div>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white"><span><?php echo count($history); ?> record(s)</span></div>
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Date</th><th>Check In</th><th>Check Out</th><th>Confidence</th><th>Status</th></tr>
            </thead>
            <tbody>
            <?php if (empty($history)): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">No attendance records found.</td></tr>
            <?php endif; ?>
            <?php foreach ($history as $r): ?>
            <tr>
                <td><?php echo htmlspecialchars($r['attendance_date']); ?></td>
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
