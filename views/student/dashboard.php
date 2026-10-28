<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2"><i class="fa-solid fa-id-card me-2"></i>Today's Status</h6>
                <?php if ($todayRecord): ?>
                    <div class="d-flex align-items-center gap-3">
                        <i class="fa-solid fa-check-circle fs-1 text-<?php echo $todayRecord['status'] === 'late' ? 'warning' : ($todayRecord['status'] === 'on_leave' ? 'info' : 'success'); ?>"></i>
                        <div>
                            <strong><?php echo strtoupper($todayRecord['status']); ?></strong>
                            <div class="small text-muted">
                                In: <?php echo htmlspecialchars(substr($todayRecord['check_in_time'], 11, 5)); ?>
                                <?php if ($todayRecord['check_out_time']): ?>
                                    &middot; Out: <?php echo htmlspecialchars(substr($todayRecord['check_out_time'], 11, 5)); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">Not yet checked in today.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2"><i class="fa-solid fa-chart-line me-2"></i>Attendance Rate</h6>
                <div class="progress my-2" style="height:20px">
                    <div class="progress-bar bg-success" style="width:<?php echo $stats['percentage']; ?>%">
                        <?php echo $stats['percentage']; ?>%
                    </div>
                </div>
                <div class="small text-muted">
                    Present: <?php echo (int)$stats['present']; ?> &middot; Late: <?php echo (int)$stats['late']; ?> &middot; Leave: <?php echo (int)$stats['leave']; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2"><i class="fa-solid fa-face-id me-2"></i>Face Status</h6>
                <?php if ($student['face_registered']): ?>
                    <p class="mb-2"><span class="badge bg-success">Registered v<?php echo (int)$student['face_registration_version']; ?></span></p>
                <?php else: ?>
                    <p class="mb-2"><span class="badge bg-warning">Not Registered</span></p>
                <?php endif; ?>
                <a href="<?php echo APP_URL; ?>/student/face" class="btn btn-sm btn-primary w-100"><i class="fa-solid fa-face-id me-1"></i>Manage Face</a>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fa-solid fa-clipboard-list me-2"></i>Recent Attendance</h6>
                <a href="<?php echo APP_URL; ?>/student/attendance" class="small text-decoration-none">View All &rarr;</a>
            </div>
            <div class="card-body">
                <?php
                // Fetch recent 5 attendance records directly
                $recentRecords = \Database::fetchAll("
                    SELECT * FROM attendance
                    WHERE student_id = ?
                    ORDER BY attendance_date DESC
                    LIMIT 5", [(int)\Session::get('student_id')]);
                ?>
                <?php if (empty($recentRecords)): ?>
                    <p class="text-center text-muted py-3">No attendance records yet. Scan your face at the kiosk to check in.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light"><tr><th>Date</th><th>Check In</th><th>Check Out</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php foreach ($recentRecords as $r): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r['attendance_date']); ?></td>
                            <td><?php echo htmlspecialchars(substr($r['check_in_time'], 11, 5)); ?></td>
                            <td><?php echo $r['check_out_time'] ? htmlspecialchars(substr($r['check_out_time'], 11, 5)) : '—'; ?></td>
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
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white"><h6 class="mb-0"><i class="fa-solid fa-calendar-minus me-2"></i>Recent Leaves</h6></div>
            <div class="card-body">
                <?php if (empty($myLeaves)): ?>
                    <p class="text-muted mb-0">No leave applications.</p>
                <?php else: ?>
                <?php foreach ($myLeaves as $lv): ?>
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom" style="border-bottom:1px solid #f1f5f9">
                        <span class="small"><?php echo htmlspecialchars($lv['leave_date']); ?></span>
                        <span class="badge bg-<?php echo $lv['status'] === 'approved' ? 'success' : ($lv['status'] === 'rejected' ? 'danger' : 'warning'); ?>">
                            <?php echo strtoupper($lv['status']); ?>
                        </span>
                    </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0"><i class="fa-solid fa-bolt me-2"></i>Quick Actions</h6></div>
            <div class="card-body">
                <a href="<?php echo APP_URL; ?>/student/attendance/stats" class="btn btn-outline-primary btn-sm w-100 mb-1"><i class="fa-solid fa-chart-line me-1"></i>Statistics</a>
                <a href="<?php echo APP_URL; ?>/student/leave" class="btn btn-outline-warning btn-sm w-100 mb-1"><i class="fa-solid fa-calendar-minus me-1"></i>Apply Leave</a>
                <a href="<?php echo APP_URL; ?>/student/profile" class="btn btn-outline-secondary btn-sm w-100"><i class="fa-solid fa-user me-1"></i>My Profile</a>
            </div>
        </div>
    </div>
</div>
