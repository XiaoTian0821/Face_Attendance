<?php
// Kiosk layout - full screen, no sidebar
$flashes = Session::getFlashes();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Kiosk'); ?> | <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo APP_URL; ?>/assets/css/app.css" rel="stylesheet">
    <style>
        body { overflow: hidden; background: #0f172a; margin:0; }
        .kiosk-container { height: 100vh; display: flex; flex-direction: column; }
        .kiosk-header {
            background: #1e293b; padding: 0.75rem 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 2px solid #334155;
        }
        .kiosk-header h1 { color: #fff; font-size: 1.25rem; margin: 0; }
        .kiosk-header h1 i { color: #3b82f6; margin-right: 0.5rem; }
        .kiosk-body { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1rem; gap: 1rem; }
        .kiosk-video-wrapper {
            position: relative; width: 100%; max-width: 640px;
            background: #000; border-radius: 12px; overflow: hidden;
            border: 2px solid #334155;
        }
        .kiosk-video-wrapper video, .kiosk-video-wrapper canvas { width: 100%; display: block; }
        .kiosk-canvas-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; }
        .kiosk-status {
            min-height: 56px; display: flex; align-items: center; justify-content: center;
            font-size: 1.15rem; font-weight: 600; text-align: center;
            padding: 0.75rem 1.5rem; border-radius: 8px;
        }
        .kiosk-status.idle      { background: #1e293b; color: #94a3b8; }
        .kiosk-status.success   { background: #065f46; color: #6ee7b7; }
        .kiosk-status.error     { background: #7f1d1d; color: #fca5a5; }
        .kiosk-status.warning   { background: #78350f; color: #fde68a; }
        .kiosk-result-card {
            background: #1e293b; border-radius: 12px; padding: 1rem 1.5rem;
            min-width: 320px; text-align: center; color: #e2e8f0;
            border: 1px solid #334155;
        }
        .kiosk-result-card .student-name { font-size: 1.4rem; font-weight: 700; }
        .kiosk-result-card .student-code { color: #94a3b8; font-size: 0.9rem; }
        .kiosk-result-card .confidence   { color: #3b82f6; font-weight: 600; }
        .kiosk-footer {
            background: #1e293b; padding: 0.5rem 1.5rem;
            display: flex; justify-content: space-between; align-items: center;
            border-top: 1px solid #334155; font-size: 0.85rem; color: #64748b;
        }
        @media (max-width: 768px) {
            .kiosk-body { padding: 0.5rem; }
            .kiosk-result-card { min-width: auto; width: 100%; }
        }
    </style>
</head>
<body>
<div class="kiosk-container">
    <div class="kiosk-header">
        <h1><i class="fa-solid fa-fingerprint"></i><?php echo htmlspecialchars($title ?? 'Attendance Kiosk'); ?></h1>
        <div>
            <span class="text-white-50 me-3"><i class="fa-solid fa-user me-1"></i><?php echo htmlspecialchars(Session::get('username', 'Operator')); ?></span>
            <a href="<?php echo APP_URL; ?>/logout" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-power-off me-1"></i>Logout</a>
        </div>
    </div>
    <div class="kiosk-body">
        <div class="kiosk-video-wrapper">
            <video id="kioskVideo" autoplay playsinline muted style="background:#000"></video>
            <canvas id="kioskOverlay" class="kiosk-canvas-overlay"></canvas>
        </div>
        <div id="kioskStatus" class="kiosk-status idle">
            <i class="fa-solid fa-circle-notch fa-spin me-2"></i>Initializing camera...
        </div>
        <div id="kioskResult" class="kiosk-result-card d-none">
            <div class="student-name" id="kioskStudentName"></div>
            <div class="student-code" id="kioskStudentCode"></div>
            <div class="confidence" id="kioskConfidence"></div>
            <div class="mt-2" id="kioskResultMsg"></div>
        </div>
    </div>
    <div class="kiosk-footer">
        <span><i class="fa-solid fa-shield-halved me-1"></i> <?php echo htmlspecialchars(Session::get('username', '')); ?></span>
        <span id="kioskClock"></span>
        <a href="<?php echo APP_URL; ?>/admin/dashboard" class="text-decoration-none text-white-50"><i class="fa-solid fa-arrow-left me-1"></i>Back</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo APP_URL; ?>/assets/vendor/face-api.min.js?v=<?php echo filemtime(BASE_PATH . '/public/assets/vendor/face-api.min.js'); ?>"></script>
<script>
window.FACE_KIOSK_DATA = {
    students: <?php echo $studentsJson ?? '[]'; ?>,
    settings: <?php echo $settingsJson ?? '{}'; ?>,
    csrfToken: '<?php echo Session::get('csrf_token', ''); ?>',
    scanUrl: '<?php echo APP_URL; ?>/kiosk/scan',
    modelsBasePath: '<?php echo APP_URL; ?>/assets/models/'
};
</script>
<script src="<?php echo APP_URL; ?>/assets/js/face/camera.js?v=<?php echo filemtime(BASE_PATH . '/public/assets/js/face/camera.js'); ?>"></script>
<script src="<?php echo APP_URL; ?>/assets/js/face/face-engine.js?v=<?php echo filemtime(BASE_PATH . '/public/assets/js/face/face-engine.js'); ?>"></script>
<script src="<?php echo APP_URL; ?>/assets/js/face/quality.js?v=<?php echo filemtime(BASE_PATH . '/public/assets/js/face/quality.js'); ?>"></script>
<script src="<?php echo APP_URL; ?>/assets/js/face/pose.js?v=<?php echo filemtime(BASE_PATH . '/public/assets/js/face/pose.js'); ?>"></script>
<script src="<?php echo APP_URL; ?>/assets/js/face/matcher.js?v=<?php echo filemtime(BASE_PATH . '/public/assets/js/face/matcher.js'); ?>"></script>
<script src="<?php echo APP_URL; ?>/assets/js/face/kiosk-flow.js?v=<?php echo filemtime(BASE_PATH . '/public/assets/js/face/kiosk-flow.js'); ?>"></script>
<script>
// Auto-start kiosk
document.addEventListener('DOMContentLoaded', function() {
    var data = window.FACE_KIOSK_DATA;
    var flow = new KioskFlow({
        modelsBasePath: data.modelsBasePath,
        students: data.students,
        settings: data.settings,
        scanUrl: data.scanUrl,
        csrfToken: data.csrfToken,
    });
    flow.init();

    // Allow Escape key to stop scanning
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') flow.stop();
    });
});
</script>
</body>
</html>
