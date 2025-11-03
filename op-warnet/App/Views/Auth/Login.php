<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['judul'] ?? 'Login'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASEURL; ?>/css/login.css?v=<?= time(); ?>">
</head>
<body class="login-page">
    <div class="login-box">
        <div class="login-logo text-center mb-4">
            <i class="bi bi-display-fill"></i>
            <h2>OP-Warnet</h2>
        </div>
        <p class="login-box-msg text-center mb-4">Silakan login untuk memulai sesi Anda</p>
        <?php if (!empty($flashes)): ?>
            <?php foreach ($flashes as $flash): ?>
                <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <form action="<?= BASEURL; ?>/index.php?url=auth/login" method="post" novalidate>
            <?= Csrf::input(); ?>
            <div class="input-group mb-3">
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required autofocus>
                <span class="input-group-text">
                    <i class="bi bi-person"></i>
                </span>
            </div>
            <div class="input-group mb-4">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <span class="input-group-text">
                    <i class="bi bi-lock"></i>
                </span>
            </div>
            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Login</button>
                </div>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>