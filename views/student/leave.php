<div class="row g-3">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fa-solid fa-calendar-minus me-2"></i>Submit Leave Application</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo APP_URL; ?>/student/leave" enctype="multipart/form-data">
                    <?php echo $csrf; ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Leave Date <span class="text-danger">*</span></label>
                        <input type="date" name="leave_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Reason <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required placeholder="Please explain why you are requesting leave..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Supporting Document <small class="text-muted">(PDF, JPG, PNG &mdash; max 5MB)</small></label>
                        <input type="file" name="document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane me-2"></i>Submit Application</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0">My Leave Applications</h6></div>
            <div class="card-body">
                <?php if (empty($leaves)): ?>
                    <p class="text-muted text-center py-3">No leave applications yet.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Date</th><th>Status</th><th>Note</th><th>Doc</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($leaves as $lv): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($lv['leave_date']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $lv['status'] === 'approved' ? 'success' : ($lv['status'] === 'rejected' ? 'danger' : 'warning'); ?>">
                                    <?php echo strtoupper($lv['status']); ?>
                                </span>
                            </td>
                            <td class="small text-muted"><?php echo htmlspecialchars($lv['review_note'] ?? ''); ?></td>
                            <td>
                                <?php if ($lv['document_path']): ?>
                                    <a href="<?php echo APP_URL; ?>/uploads/<?php echo htmlspecialchars($lv['document_path']); ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-file"></i></a>
                                <?php else: ?>—<?php endif; ?>
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
</div>
