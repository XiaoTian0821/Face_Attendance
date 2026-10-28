<?php
// Auth layout - centered card style
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
<body class="auth-body">
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-icon"><i class="fa-solid fa-fingerprint"></i></div>
            <h2 class="auth-title"><?php echo htmlspecialchars($title ?? APP_NAME); ?></h2>
            <p class="auth-subtitle text-muted mb-4"><?php echo $subtitle ?? 'Sign in to your account'; ?></p>
        </div>

        <?php
        $flashes = Session::getFlashes();
        $flashSuccess = ''; $flashError = '';
        foreach ($flashes as $f) {
            if ($f['type'] === 'success') $flashSuccess = $f['message'];
            if ($f['type'] === 'error') $flashError = $f['message'];
        }
        ?>
        <?php if ($flashError): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fa-solid fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($flashError); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if ($flashSuccess): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fa-solid fa-check-circle me-2"></i><?php echo htmlspecialchars($flashSuccess); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php
        $viewFile = BASE_PATH . '/views/' . ($content_file ?? '') . '.php';
        if (file_exists($viewFile)) require $viewFile;
        ?>
    </div>
    <div class="auth-footer text-muted small">
        &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?> &mdash;
        <a href="<?php echo APP_URL; ?>/privacy" class="link-secondary">Privacy Policy</a>
    </div>
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
