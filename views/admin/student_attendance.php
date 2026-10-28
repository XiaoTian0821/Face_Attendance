<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fa-solid fa-users me-2"></i>Student Attendance</h6>
        <a href="<?php echo APP_URL; ?>/admin/attendance" class="btn btn-sm btn-outline-secondary">All Records</a>
    </div>
    <div class="card-body">
        <div class="row g-3 mb-3">
            <div class="col-3">
                <div class="card border-0"><div class="card-body text-center">
                    <div class="fs-3 fw-bold text-success"><?php echo (int)$stats['present']; ?></div>
                    <small class="text-muted">Present Days</small>
                </div></div>
            </div>
            <div class="col-3">
                <div class="card border-0"><div class="card-body text-center">
                    <div class="fs-3 fw-bold text-warning"><?php echo (int)$stats['late']; ?></div>
                    <small class="text-muted">Late Days</small>
                </div></div>
            </div>
            <div class="col-3">
                <div class="card border-0"><div class="card-body text-center">
                    <div class="fs-3 fw-bold text-info"><?php echo (int)$stats['leave']; ?></div>
                    <small class="text-muted">Leave Days</small>
                </div></div>
            </div>
            <div class="col-3">
                <div class="card border-0"><div class="card-body text-center">
                    <div class="fs-3 fw-bold text-primary"><?php echo $stats['percentage']; ?>%</div>
                    <small class="text-muted">Attendance Rate</small>
                </div></div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light"><tr><th>Date</th><th>Check In</th><th>Check Out</th><th>Confidence</th><th>Status</th></tr></thead>
                <tbody>
                <?php if (empty($history)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-3">No attendance records.</td></tr>
                <?php endif; ?>
                <?php foreach ($history as $r): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r['attendance_date']); ?></td>
                        <td><?php echo htmlspecialchars($r['check_in_time']); ?></td>
                        <td><?php echo htmlspecialchars($r['check_out_time'] ?? '—'); ?></td>
                        <td><?php echo $r['check_in_confidence'] ? round($r['check_in_confidence'], 3) : '—'; ?></td>
                        <td><span class="badge bg-<?php echo $r['status'] === 'late' ? 'warning' : ($r['status'] === 'on_leave' ? 'info' : 'success'); ?>"><?php echo strtoupper($r['status']); ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
