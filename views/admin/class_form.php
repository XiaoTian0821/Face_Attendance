<div class="card border-0 shadow-sm">
    <div class="card-header bg-white"><h6 class="mb-0"><i class="fa-solid fa-school me-2"></i><?php echo $class ? 'Edit Class' : 'Add Class'; ?></h6></div>
    <div class="card-body">
        <form method="POST" action="<?php echo $class ? APP_URL . '/admin/classes/' . $class['id'] . '/update' : APP_URL . '/admin/classes/store'; ?>">
            <?php echo $csrf; ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Class Code <span class="text-danger">*</span></label>
                    <input type="text" name="class_code" class="form-control" required value="<?php echo htmlspecialchars($class['class_code'] ?? ''); ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-bold">Class Name <span class="text-danger">*</span></label>
                    <input type="text" name="class_name" class="form-control" required value="<?php echo htmlspecialchars($class['class_name'] ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Start Time</label>
                    <input type="time" name="start_time" class="form-control" value="<?php echo htmlspecialchars(substr($class['start_time'] ?? '08:00:00', 0, 5)); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Late Grace (min)</label>
                    <input type="number" name="late_grace_minutes" class="form-control" min="0" value="<?php echo (int)($class['late_grace_minutes'] ?? 15); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Rest Start</label>
                    <input type="time" name="rest_start_time" class="form-control" value="<?php echo htmlspecialchars(substr($class['rest_start_time'] ?? '', 0, 5)); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Rest End</label>
                    <input type="time" name="rest_end_time" class="form-control" value="<?php echo htmlspecialchars(substr($class['rest_end_time'] ?? '', 0, 5)); ?>">
                </div>
            </div>
            <hr>
            <h6>Subjects for this Class</h6>
            <div class="row g-2 mb-3">
                <?php foreach (($subjects ?? []) as $sub):
                    $isAssigned = isset($assignedSubjects) && in_array($sub['id'], array_column($assignedSubjects, 'id'));
                ?>
                <div class="col-auto">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="subject_ids[]" value="<?php echo $sub['id']; ?>" id="sub_<?php echo $sub['id']; ?>" <?php echo $isAssigned ? 'checked' : ''; ?>>
                        <label class="form-check-label small" for="sub_<?php echo $sub['id']; ?>">
                            <span class="text-muted"><?php echo htmlspecialchars($sub['subject_code']); ?></span>
                            <?php echo htmlspecialchars($sub['subject_name']); ?>
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($subjects)): ?><p class="text-muted">No subjects available. <a href="<?php echo APP_URL; ?>/admin/subjects/create">Add a subject</a> first.</p><?php endif; ?>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check me-2"></i><?php echo $class ? 'Update' : 'Create'; ?> Class</button>
                <a href="<?php echo APP_URL; ?>/admin/classes" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
