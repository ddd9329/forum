<?php
require_once __DIR__ . '/../inc/header.php';

$u = trim($_POST['u'] ?? '');
$p = $_POST['p'] ?? '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf'] ?? '')) $errors[] = 'Błędny token formularza.';
    if ($u === '' || $p === '') $errors[] = 'Podaj login/e-mail i hasło.';
    if (!$errors) {
        if (login($u, $p)) {
            flash('success', 'Zalogowano.');
            header('Location: ' . base_url('index.php'));
            exit;
        } else {
            $errors[] = 'Błędne dane logowania.';
        }
    }
}
?>
<h1 class="h3 mb-3">Logowanie</h1>
<?php foreach ($errors as $e): ?>
  <div class="alert alert-danger"><?php echo htmlspecialchars($e); ?></div>
<?php endforeach; ?>
<form method="post" class="row gy-3">
  <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
  <div class="col-12">
    <label class="form-label">Login lub e-mail</label>
    <input type="text" name="u" class="form-control" value="<?php echo htmlspecialchars($u); ?>" required>
  </div>
  <div class="col-12">
    <label class="form-label">Hasło</label>
    <input type="password" name="p" class="form-control" required>
  </div>
  <div class="col-12">
    <button class="btn btn-primary" type="submit">Zaloguj</button>
    <a class="btn btn-secondary" href="<?php echo htmlspecialchars(base_url('index.php')); ?>">Anuluj</a>
  </div>
</form>
<?php require_once __DIR__ . '/../inc/footer.php'; ?>
