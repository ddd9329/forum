<?php
require_once __DIR__ . '/auth.php';
?>
<!doctype html>
<html lang="pl">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Proste forum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
      <a class="navbar-brand" href="<?php echo htmlspecialchars(base_url('index.php')); ?>">Forum</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample" aria-controls="navbarsExample" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarsExample">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars(base_url('index.php')); ?>">Wpisy</a></li>
          <?php if (is_logged_in()): ?>
            <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars(base_url('post_create.php')); ?>">Nowy wpis</a></li>
          <?php endif; ?>
        </ul>
        <ul class="navbar-nav ms-auto">
          <?php if (is_logged_in()): ?>
            <li class="nav-item"><span class="navbar-text me-3">Zalogowany: <strong><?php echo htmlspecialchars(current_user()['username']); ?></strong><?php echo is_admin() ? ' (admin)' : ''; ?></span></li>
            <li class="nav-item"><a class="btn btn-outline-light btn-sm" href="<?php echo htmlspecialchars(base_url('logout.php')); ?>">Wyloguj</a></li>
          <?php else: ?>
            <li class="nav-item me-2"><a class="btn btn-outline-light btn-sm" href="<?php echo htmlspecialchars(base_url('login.php')); ?>">Logowanie</a></li>
            <li class="nav-item"><a class="btn btn-warning btn-sm" href="<?php echo htmlspecialchars(base_url('register.php')); ?>">Rejestracja</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
  <main class="container">
  <?php foreach (flashes() as $f): ?>
    <div class="alert alert-<?php echo htmlspecialchars($f['t']); ?> alert-dismissible fade show" role="alert">
      <?php echo htmlspecialchars($f['m']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endforeach; ?>
