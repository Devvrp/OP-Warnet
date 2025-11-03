<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman <?= htmlspecialchars($data['judul'] ?? 'Login'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASEURL; ?>/css/login.css">
</head>
<body class="text-center">
    
<main class="form-signin">
    <form action="<?= BASEURL; ?>/index.php?url=auth/login" method="post">
    <?= Csrf::input(); ?>
    <h1 class="h3 mb-3 fw-bold">🚀 OP WARNET</h1>
    <h2 class="h5 mb-3 fw-normal">Silakan login</h2>
    <?php if (!empty($flashes)): ?>
        <?php foreach ($flashes as $flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <div class="form-floating">
        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required autofocus>
        <label for="username">Username</label>
    </div>
    <div class="form-floating">
        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
        <label for="password">Password</label>
    </div>
    <button class="w-100 btn btn-lg btn-primary" type="submit">Login</button>
    <p class="mt-4 text-muted">Gunakan: admin/admin123 | operator/operator123 | maintenance/mtc123</p>
    <p class="mt-5 mb-3 text-muted">&copy; 2025 OP Warnet</p>
    </form>
</main>

</body>
</html>