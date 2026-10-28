<div class="card border-0 shadow-sm">
    <div class="card-header bg-white"><h6 class="mb-0"><i class="fa-solid fa-book me-2"></i>Add Subject</h6></div>
    <div class="card-body">
        <form method="POST" action="<?php echo APP_URL; ?>/admin/subjects/store">
            <?php echo $csrf; ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Subject Code <span class="text-danger">*</span></label>
                    <input type="text" name="subject_code" class="form-control" required placeholder="INTRO">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-bold">Subject Name <span class="text-danger">*</span></label>
                    <input type="text" name="subject_name" class="form-control" required placeholder="Introduction to Programming">
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Brief description..."></textarea>
                </div>
            </div>
            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check me-2"></i>Create Subject</button>
                <a href="<?php echo APP_URL; ?>/admin/subjects" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
