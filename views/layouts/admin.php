<?php
// Admin layout with sidebar
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
<body class="d-flex">
<aside class="sidebar d-flex flex-column" id="sidebar">
    <div class="sidebar-header p-3">
        <a href="<?php echo APP_URL; ?>/admin/dashboard" class="sidebar-brand text-white text-decoration-none">
            <i class="fa-solid fa-fingerprint me-2"></i><strong><?php echo APP_NAME; ?></strong>
        </a>
        <small class="text-white-50">Admin Portal</small>
    </div>
    <nav class="sidebar-nav flex-grow-1 overflow-auto py-2">
        <?php
        $navItems = [
            '/admin/dashboard' => ['Dashboard', 'fa-gauge-high'],
            '/admin/students' => ['Students', 'fa-users'],
            '/admin/teachers' => ['Teachers', 'fa-user-tie'],
            '/admin/classes' => ['Classes', 'fa-school'],
            '/admin/subjects' => ['Subjects', 'fa-book'],
            '/admin/attendance' => ['Attendance', 'fa-clipboard-check'],
            '/admin/leave' => ['Leave Requests', 'fa-calendar-minus'],
            '/admin/users' => ['Users', 'fa-user-circle'],
            '/kiosk' => ['Kiosk', 'fa-desktop'],
            '/admin/audit' => ['Audit Logs', 'fa-file-shield'],
            '/admin/settings' => ['Settings', 'fa-gear'],
        ];
        $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        foreach ($navItems as $href => [$label, $icon]):
            $active = strpos($currentPath, strtok($href, '/')) !== false;
        ?>
        <a href="<?php echo APP_URL . $href; ?>" class="sidebar-link <?php echo $active ? 'active' : ''; ?>">
            <i class="fa-solid <?php echo $icon; ?>"></i><span><?php echo $label; ?></span>
        </a>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-footer p-3 border-top">
        <div class="d-flex align-items-center mb-2">
            <i class="fa-solid fa-user-circle me-2 text-white-50"></i>
            <span class="text-white small"><?php echo htmlspecialchars(Session::get('username', 'Admin')); ?></span>
        </div>
        <a href="<?php echo APP_URL; ?>/logout" class="sidebar-link text-danger mb-1">
            <i class="fa-solid fa-sign-out-alt me-2"></i>Logout
        </a>
        <a href="<?php echo APP_URL; ?>/privacy" class="sidebar-link text-white-50" target="_blank">
            <i class="fa-solid fa-shield-halved me-2"></i>Privacy Policy
        </a>
    </div>
</aside>
<main class="main-content flex-grow-1 d-flex flex-column">
    <div class="topbar d-flex align-items-center p-3 border-bottom bg-white">
        <button class="btn btn-light sidebar-toggle me-3" id="sidebarToggle"><i class="fa-solid fa-bars"></i></button>
        <h5 class="m-0 me-auto page-title"><?php echo htmlspecialchars($title ?? ''); ?></h5>
        <span class="badge bg-danger">Admin</span>
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
        <!-- Page content injected here -->
        <?php
        $contentFile = $content_file ?? null;
        if ($contentFile) {
            $contentData = $content_data ?? [];
            $contentVars = $contentData;
            $viewPath = BASE_PATH . '/views/' . $contentFile . '.php';
            if (file_exists($viewPath)) {
                require $viewPath;
            } else {
                echo '<div class="alert alert-warning">View not found: ' . htmlspecialchars($contentFile) . '</div>';
            }
        }
        ?>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('sidebarToggle')?.addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('show-mobile');
});
</script>
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
