<div class="card border-0 shadow-sm">
    <div class="card-header bg-white"><h6 class="mb-0"><i class="fa-solid fa-user-tie me-2"></i><?php echo $teacher ? 'Edit Teacher' : 'Add Teacher'; ?></h6></div>
    <div class="card-body">
        <form method="POST" action="<?php echo $teacher ? APP_URL . '/admin/teachers/' . $teacher['id'] . '/update' : APP_URL . '/admin/teachers/store'; ?>">
            <?php echo $csrf; ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Teacher Code <span class="text-danger">*</span></label>
                    <input type="text" name="teacher_code" class="form-control" required value="<?php echo htmlspecialchars($teacher['teacher_code'] ?? ''); ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="full_name" class="form-control" required value="<?php echo htmlspecialchars($teacher['full_name'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($teacher['email'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($teacher['phone'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Link to User Account</label>
                    <select name="user_id" class="form-select">
                        <option value="0">— No Link —</option>
                        <?php foreach (($teacherUsers ?? []) as $u): ?>
                        <option value="<?php echo $u['id']; ?>" <?php echo $teacher && $teacher['user_id'] == $u['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($u['username']); ?> (<?php echo htmlspecialchars($u['email']); ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check me-2"></i><?php echo $teacher ? 'Update' : 'Create'; ?> Teacher</button>
                <a href="<?php echo APP_URL; ?>/admin/teachers" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
