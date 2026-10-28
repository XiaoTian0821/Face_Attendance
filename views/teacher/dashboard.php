<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm"><div class="card-body text-center">
            <i class="fa-solid fa-users fs-2 text-primary mb-1"></i>
            <div class="fs-3 fw-bold"><?php echo count($students); ?></div>
            <small class="text-muted">Students Assigned</small>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm"><div class="card-body text-center">
            <i class="fa-solid fa-school fs-2 text-success mb-1"></i>
            <div class="fs-3 fw-bold"><?php echo count($todaySummary); ?></div>
            <small class="text-muted">Classes Today</small>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm"><div class="card-body text-center">
            <i class="fa-solid fa-book fs-2 text-info mb-1"></i>
            <div class="fs-3 fw-bold"><?php echo count($assignments); ?></div>
            <small class="text-muted">Assignments</small>
        </div></div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0"><i class="fa-solid fa-clipboard-check me-2"></i>Today's Attendance Summary</h6></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Class</th><th>Present</th><th>Total</th><th>Rate</th></tr>
                        </thead>
                        <tbody>
                        <?php if (empty($todaySummary)): ?>
                            <tr><td colspan="4" class="text-center text-muted">No class assignments yet.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($todaySummary as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['class_name']); ?></td>
                            <td class="fw-bold text-success"><?php echo $row['present']; ?></td>
                            <td><?php echo $row['total']; ?></td>
                            <td>
                                <?php $rate = $row['total'] > 0 ? round(($row['present']/$row['total'])*100) : 0; ?>
                                <div class="progress progress-sm" style="height:6px;width:80px">
                                    <div class="progress-bar bg-success" style="width:<?php echo $rate; ?>%"></div>
                                </div>
                                <small><?php echo $rate; ?>%</small>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0"><i class="fa-solid fa-book me-2"></i>My Assignments</h6></div>
            <div class="card-body">
                <?php if (empty($assignments)): ?>
                    <p class="text-muted mb-0">No class/subject assignments yet.</p>
                <?php else: ?>
                <table class="table table-sm mb-0">
                    <?php foreach ($assignments as $a): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($a['class_name']); ?></td>
                        <td class="text-muted small"><?php echo htmlspecialchars($a['subject_name'] ?? 'All Subjects'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fa-solid fa-users me-2"></i>My Students</h6>
                <a href="<?php echo APP_URL; ?>/teacher/attendance" class="small">View Attendance</a>
            </div>
            <div class="card-body">
                <?php if (empty($students)): ?>
                    <p class="text-muted mb-0">No students assigned to your classes yet.</p>
                <?php else: ?>
                <div class="table-responsive" style="max-height:320px">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light sticky-top"><tr><th>Code</th><th>Name</th><th>Class</th><th>Face</th></tr></thead>
                        <tbody>
                        <?php foreach ($students as $s): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($s['student_code']); ?></td>
                            <td><a href="<?php echo APP_URL; ?>/teacher/students/<?php echo $s['id']; ?>" class="text-decoration-none"><?php echo htmlspecialchars($s['full_name']); ?></a></td>
                            <td><?php echo htmlspecialchars($s['class_code'] ?? '—'); ?></td>
                            <td><?php echo $s['face_registered'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
