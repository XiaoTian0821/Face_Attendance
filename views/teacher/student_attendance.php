<div class="row g-3">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0">Statistics</h6></div>
            <div class="card-body">
                <div class="mb-2">
                    <div class="d-flex justify-content-between"><small>Total</small><strong><?php echo (int)$stats['total']; ?></strong></div>
                    <div class="progress" style="height:4px"><div class="progress-bar bg-primary" style="width:100%"></div></div>
                </div>
                <div class="mb-2">
                    <div class="d-flex justify-content-between"><small>Present</small><strong class="text-success"><?php echo (int)$stats['present']; ?></strong></div>
                    <div class="progress" style="height:4px"><div class="progress-bar bg-success" style="width:<?php echo $stats['total']?round($stats['present']/$stats['total']*100):0; ?>%"></div></div>
                </div>
                <div class="mb-2">
                    <div class="d-flex justify-content-between"><small>Late</small><strong class="text-warning"><?php echo (int)$stats['late']; ?></strong></div>
                    <div class="progress" style="height:4px"><div class="progress-bar bg-warning" style="width:<?php echo $stats['total']?round($stats['late']/$stats['total']*100):0; ?>%"></div></div>
                </div>
                <div class="mb-2">
                    <div class="d-flex justify-content-between"><small>Leave</small><strong class="text-info"><?php echo (int)$stats['leave']; ?></strong></div>
                    <div class="progress" style="height:4px"><div class="progress-bar bg-info" style="width:<?php echo $stats['total']?round($stats['leave']/$stats['total']*100):0; ?>%"></div></div>
                </div>
                <hr>
                <div class="text-center">
                    <div class="fs-3 fw-bold text-primary"><?php echo $stats['percentage']; ?>%</div>
                    <small class="text-muted">Attendance Rate</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0">History</h6>
                <a href="<?php echo APP_URL; ?>/teacher/attendance" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Back</a>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height:400px">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light sticky-top"><tr><th>Date</th><th>In</th><th>Out</th><th>Conf.</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php if (empty($history)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-3">No records.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($history as $r): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r['attendance_date']); ?></td>
                            <td><?php echo htmlspecialchars(substr($r['check_in_time'], 11, 5)); ?></td>
                            <td><?php echo $r['check_out_time'] ? htmlspecialchars(substr($r['check_out_time'], 11, 5)) : '—'; ?></td>
                            <td><?php echo $r['check_in_confidence'] ? round($r['check_in_confidence'], 3) : '—'; ?></td>
                            <td><span class="badge bg-<?php echo $r['status']==='late'?'warning':($r['status']==='on_leave'?'info':'success'); ?>"><?php echo strtoupper($r['status']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
