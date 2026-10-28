<div class="card border-0 shadow-sm">
    <div class="card-header bg-white"><h6 class="mb-0"><i class="fa-solid fa-gear me-2"></i>Face Recognition Settings</h6></div>
    <div class="card-body">
        <form method="POST" action="<?php echo APP_URL; ?>/admin/settings/update">
            <?php echo $csrf; ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Match Threshold</label>
                    <input type="number" step="0.01" name="match_threshold" class="form-control" value="<?php echo $settings['match_threshold']; ?>" min="0" max="1">
                    <small class="text-muted">Euclidean distance threshold (lower = stricter)</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Minimum Confidence</label>
                    <input type="number" step="0.01" name="min_confidence" class="form-control" value="<?php echo $settings['min_confidence']; ?>" min="0" max="1">
                    <small class="text-muted">Minimum attendance confidence required</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Detection Confidence</label>
                    <input type="number" step="0.01" name="detection_confidence" class="form-control" value="<?php echo $settings['detection_confidence']; ?>" min="0" max="1">
                    <small class="text-muted">Face detection model confidence</small>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Min Face Size (px)</label>
                    <input type="number" name="min_face_size" class="form-control" value="<?php echo $settings['min_face_size']; ?>" min="10">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Max Face Size (px)</label>
                    <input type="number" name="max_face_size" class="form-control" value="<?php echo $settings['max_face_size']; ?>" min="10">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Min Brightness</label>
                    <input type="number" name="min_brightness" class="form-control" value="<?php echo $settings['min_brightness']; ?>" min="0" max="255">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Max Brightness</label>
                    <input type="number" name="max_brightness" class="form-control" value="<?php echo $settings['max_brightness']; ?>" min="0" max="255">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Min Sharpness</label>
                    <input type="number" name="min_sharpness" class="form-control" value="<?php echo $settings['min_sharpness']; ?>" min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Stable Frames</label>
                    <input type="number" name="stable_frames" class="form-control" value="<?php echo $settings['stable_frames']; ?>" min="1">
                    <small class="text-muted">Frames needed before capture</small>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Registration Samples</label>
                    <input type="number" name="registration_samples" class="form-control" value="<?php echo $settings['registration_samples']; ?>" min="1" max="10">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Dup Scan Interval (sec)</label>
                    <input type="number" name="duplicate_scan_interval" class="form-control" value="<?php echo $settings['duplicate_scan_interval']; ?>" min="0">
                </div>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check me-2"></i>Save Settings</button>
        </form>
    </div>
</div>
