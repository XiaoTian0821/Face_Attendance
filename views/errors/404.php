<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title ?? 'Error'; ?> | <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center bg-light min-vh-100">
<div class="text-center p-4">
    <div class="fs-1 mb-3"><i class="fa-solid fa-magnifying-glass text-secondary"></i></div>
    <h1 class="display-4 fw-bold">404</h1>
    <h3>Page Not Found</h3>
    <p class="text-muted">The page you're looking for doesn't exist.</p>
    <a href="<?php echo APP_URL; ?>/login" class="btn btn-primary mt-3"><i class="fa-solid fa-home me-2"></i>Go Home</a>
</div>
</body>
</html>
