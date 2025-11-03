<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman <?= $data['judul'] ?? 'OP Warnet'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASEURL; ?>/css/style.css?v=<?= time(); ?>">
</head>
<body>
<nav class="navbar navbar-expand-lg border-bottom" data-bs-theme="dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= BASEURL; ?>/index.php">🚀 OP WARNET</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php $role = $_SESSION['user_role'] ?? null; ?>
        <li class="nav-item">
          <a class="nav-link <?= ($active === 'home') ? 'active fw-semibold' : ''; ?>" href="<?= BASEURL; ?>/index.php">Dashboard</a>
        </li>
        <?php if ($role === 'admin' || $role === 'maintenance'): ?>
        <li class="nav-item">
          <a class="nav-link <?= ($active === 'computers') ? 'active fw-semibold' : ''; ?>" href="<?= BASEURL; ?>/index.php?url=computers">Manajemen PC</a>
        </li>
        <?php endif; ?>
        <?php if ($role === 'admin' || $role === 'operator'): ?>
        <li class="nav-item">
          <a class="nav-link <?= ($active === 'products' && (!isset($_GET['url']) || explode('/', $_GET['url'])[1] !== 'recycleBin')) ? 'active fw-semibold' : ''; ?>" href="<?= BASEURL; ?>/index.php?url=products">Manajemen Produk</a>
        </li>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav">
        <?php if ($role === 'admin'): ?>
        <li class="nav-item">
            <a class="nav-link <?= ($active === 'products' && isset($_GET['url']) && explode('/', $_GET['url'])[1] === 'recycleBin') ? 'active fw-semibold' : ''; ?>" href="<?= BASEURL; ?>/index.php?url=products/recycleBin" title="Recycle Bin">
                <i class="bi bi-trash3"></i> <span class="d-lg-none ms-1">Recycle Bin</span>
            </a>
        </li>
        <?php endif; ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['user_nama'] ?? 'User'); ?> (<?= htmlspecialchars($role); ?>)
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <form action="<?= BASEURL; ?>/index.php?url=home/logout" method="post" id="logoutForm">
                    <?= Csrf::input(); ?>
                    <a class="dropdown-item" href="#" onclick="document.getElementById('logoutForm').submit(); return false;">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </a>
                </form>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-3">
    <?php if (!empty($flashes)): ?>
        <?php foreach ($flashes as $flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show shadow-sm" role="alert">
                <?= htmlspecialchars($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>