<?php
// Teacher layout
$flashes = Session::getFlashes();
$flashSuccess = '';
$flashError = '';
foreach ($flashes as $f) {
    if ($f['type'] === 'success') $flashSuccess = $f['message'];
    if ($f['type'] === 'error') $flashError = $f['message'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? APP_NAME); ?> | <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo APP_URL; ?>/assets/css/app.css" rel="stylesheet">
</head>
<body>
<div class="d-flex">
<aside class="sidebar">
    <div class="sidebar-header p-3">
        <a href="<?php echo APP_URL; ?>/teacher/dashboard" class="sidebar-brand text-white text-decoration-none">
            <i class="fa-solid fa-fingerprint me-2"></i><strong><?php echo APP_NAME; ?></strong>
        </a>
        <small class="text-white-50">Teacher Portal</small>
    </div>
    <nav class="sidebar-nav py-2">
        <a href="<?php echo APP_URL; ?>/teacher/dashboard" class="sidebar-link <?php echo str_contains($_SERVER['REQUEST_URI'] ?? '', 'dashboard') ? 'active' : ''; ?>">
            <i class="fa-solid fa-gauge-high"></i><span>Dashboard</span>
        </a>
        <a href="<?php echo APP_URL; ?>/teacher/attendance" class="sidebar-link <?php echo str_contains($_SERVER['REQUEST_URI'] ?? '', 'attendance') ? 'active' : ''; ?>">
            <i class="fa-solid fa-clipboard-check"></i><span>Attendance</span>
        </a>
    </nav>
    <div class="sidebar-footer p-3 border-top">
        <div class="d-flex align-items-center mb-2">
            <i class="fa-solid fa-user-tie me-2 text-white-50"></i>
            <span class="text-white small"><?php echo htmlspecialchars(Session::get('username', 'Teacher')); ?></span>
        </div>
        <a href="<?php echo APP_URL; ?>/logout" class="sidebar-link text-danger mb-1">
            <i class="fa-solid fa-sign-out-alt me-2"></i>Logout
        </a>
        <a href="<?php echo APP_URL; ?>/privacy" class="sidebar-link text-white-50" target="_blank">
            <i class="fa-solid fa-shield-halved me-2"></i>Privacy
        </a>
    </div>
</aside>
<main class="main-content flex-grow-1 d-flex flex-column">
    <div class="topbar d-flex align-items-center p-3 border-bottom bg-white">
        <button class="btn btn-light me-3 sidebar-toggle d-lg-none" id="sidebarToggle"><i class="fa-solid fa-bars"></i></button>
        <h5 class="m-0 me-auto"><?php echo htmlspecialchars($title ?? ''); ?></h5>
        <span class="badge bg-success">Teacher</span>
    </div>
    <div class="content flex-grow-1 p-3 p-lg-4">
        <?php if ($flashSuccess): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fa-solid fa-check-circle me-2"></i><?php echo htmlspecialchars($flashSuccess); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if ($flashError): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fa-solid fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($flashError); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php
        $viewFile = BASE_PATH . '/views/' . ($content_file ?? '') . '.php';
        if (file_exists($viewFile)) require $viewFile;
        ?>
    </div>
</main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php if (isset($content_scripts)): ?>
<?php foreach (is_array($content_scripts) ? $content_scripts : [$content_scripts] as $script): ?>
<script src="<?php echo APP_URL; ?>/assets/js/<?php echo htmlspecialchars($script); ?>"></script>
<?php endforeach; ?>
<?php endif; ?>
<?php if (isset($inline_script)): ?>
<script><?php echo $inline_script; ?></script>
<?php endif; ?>
</body>
</html>
