<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div>
            <h6 class="mb-0"><i class="fa-solid fa-face-id me-2"></i>My Face Registration</h6>
            <small class="text-muted"><?php echo htmlspecialchars($student['full_name']); ?> &mdash; <?php echo htmlspecialchars($student['student_code']); ?></small>
        </div>
        <?php if ($registration): ?>
        <span class="badge bg-success">Registered v<?php echo (int)$registration['registration_version']; ?></span>
        <?php else: ?>
        <span class="badge bg-warning">Not Registered</span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-7">
                <div class="position-relative" style="background:#000;border-radius:8px;overflow:hidden;max-height:380px">
                    <video id="faceRegVideo" autoplay playsinline muted style="width:100%;display:block;background:#000"></video>
                    <canvas id="faceRegCanvas" class="position-absolute top-0 start-0" style="pointer-events:none;width:100%;height:100%"></canvas>
                </div>
                <div id="faceRegStatus" class="alert alert-secondary mt-3 text-center">
                    <i class="fa-solid fa-video me-2"></i>Click Start Camera to begin
                </div>
                <div class="mt-3">
                    <h6 class="mb-2">Samples: <span id="sampleCount">0</span>/<?php echo (int)$settings['registration_samples']; ?></h6>
                    <div id="sampleThumbs" class="d-flex gap-2 flex-wrap"></div>
                </div>
                <div class="mt-3 d-flex gap-2">
                    <button id="btnStartCamera" class="btn btn-primary"><i class="fa-solid fa-video me-1"></i>Start Camera</button>
                    <button id="btnStopCamera" class="btn btn-outline-danger d-none"><i class="fa-solid fa-stop me-1"></i>Stop</button>
                    <button id="btnCapture" class="btn btn-success d-none" disabled><i class="fa-solid fa-camera me-1"></i>Capture</button>
                    <button id="btnSubmitReg" class="btn btn-success d-none"><i class="fa-solid fa-check me-1"></i>Submit</button>
                </div>
            </div>
            <div class="col-md-5">
                <div class="alert alert-info">
                    <h6 class="mb-2"><i class="fa-solid fa-circle-info me-2"></i>Guidelines</h6>
                    <ul class="mb-0 small ps-3">
                        <li>Good lighting on your face</li>
                        <li>Face the camera directly</li>
                        <li>Keep face inside frame</li>
                        <li>Capture all samples</li>
                    </ul>
                </div>
                <?php if (!empty($registration) && $registration['preview_image']): ?>
                <div class="card border-0 mb-3">
                    <div class="card-header bg-white py-2"><small class="fw-bold">Last Registration</small></div>
                    <div class="card-body text-center">
                        <img src="<?php echo APP_URL; ?>/uploads/<?php echo htmlspecialchars($registration['preview_image']); ?>" class="img-thumbnail" style="max-height:150px" alt="Face preview">
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <form id="faceRegForm" method="POST" action="<?php echo APP_URL; ?>/student/face/submit" style="display:none">
            <?php echo $csrf; ?>
            <input type="hidden" name="descriptors" id="hiddenDescriptors" value="">
            <input type="hidden" name="preview_image" id="hiddenPreview" value="">
        </form>
    </div>
</div>

<script>
window.FACE_REG_DATA = {
    settings: <?php echo $settingsJson; ?>,
    csrfToken: '<?php echo Session::get('csrf_token',''); ?>',
    samplesNeeded: <?php echo (int)$settings['registration_samples']; ?>,
    submitUrl: '<?php echo APP_URL; ?>/student/face/submit',
    modelsBasePath: '<?php echo APP_URL; ?>/assets/models/'
};
</script>

<script src="<?php echo APP_URL; ?>/assets/vendor/face-api.min.js?v=<?php echo filemtime(BASE_PATH . '/public/assets/vendor/face-api.min.js'); ?>"></script>
<script src="<?php echo APP_URL; ?>/assets/js/face/camera.js?v=<?php echo filemtime(BASE_PATH . '/public/assets/js/face/camera.js'); ?>"></script>
<script src="<?php echo APP_URL; ?>/assets/js/face/face-engine.js?v=<?php echo filemtime(BASE_PATH . '/public/assets/js/face/face-engine.js'); ?>"></script>
<script src="<?php echo APP_URL; ?>/assets/js/face/quality.js?v=<?php echo filemtime(BASE_PATH . '/public/assets/js/face/quality.js'); ?>"></script>
<script src="<?php echo APP_URL; ?>/assets/js/face/pose.js?v=<?php echo filemtime(BASE_PATH . '/public/assets/js/face/pose.js'); ?>"></script>
<script src="<?php echo APP_URL; ?>/assets/js/face/registration-flow.js?v=<?php echo filemtime(BASE_PATH . '/public/assets/js/face/registration-flow.js'); ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var d = window.FACE_REG_DATA;
    var flow = new RegistrationFlow({
        modelsBasePath: d.modelsBasePath,
        settings: d.settings,
        samplesNeeded: d.samplesNeeded,
        submitUrl: d.submitUrl,
        csrfToken: d.csrfToken,
        videoId: 'faceRegVideo',
        canvasId: 'faceRegCanvas',
        statusId: 'faceRegStatus',
        sampleCountId: 'sampleCount',
        sampleThumbsId: 'sampleThumbs',
        btnStartId: 'btnStartCamera',
        btnStopId: 'btnStopCamera',
        btnCaptureId: 'btnCapture',
        btnSubmitId: 'btnSubmitReg',
    });
    flow.init();
    window.addEventListener('beforeunload', function() { flow.destroy(); });
});
</script>
