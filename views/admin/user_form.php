<div class="card border-0 shadow-sm">
    <div class="card-header bg-white"><h6 class="mb-0"><i class="fa-solid fa-user-plus me-2"></i>Add New User</h6></div>
    <div class="card-body">
        <form method="POST" action="<?php echo APP_URL; ?>/admin/users/store">
            <?php echo $csrf; ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" required minlength="8">
                    <small class="text-muted">Min 8 characters</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Link to Existing Code (optional)</label>
                    <input type="text" name="link_code" class="form-control" placeholder="STU-001 or TCH-001">
                </div>
            </div>
            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check me-2"></i>Create User</button>
                <a href="<?php echo APP_URL; ?>/admin/users" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
