<div class="card border-0 shadow-sm">
    <div class="card-header bg-white"><h6 class="mb-0"><i class="fa-solid fa-user-plus me-2"></i>Add New Student</h6></div>
    <div class="card-body">
        <form method="POST" action="<?php echo APP_URL; ?>/admin/students/store">
            <?php echo $csrf; ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Student Code <span class="text-danger">*</span></label>
                    <input type="text" name="student_code" class="form-control" required placeholder="STU-003">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="full_name" class="form-control" required placeholder="John Doe">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="student@school.edu">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" placeholder="123-456-7890">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="">—</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Class</label>
                    <select name="class_id" class="form-select">
                        <option value="">— None —</option>
                        <?php foreach ($classes as $cls): ?>
                        <option value="<?php echo $cls['id']; ?>"><?php echo htmlspecialchars($cls['class_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check me-2"></i>Create Student</button>
                <a href="<?php echo APP_URL; ?>/admin/students" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
