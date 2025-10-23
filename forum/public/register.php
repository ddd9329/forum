<?php
require_once __DIR__ . '/../inc/header.php';

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$password2 = $_POST['password2'] ?? '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf'] ?? '')) $errors[] = 'Błędny token formularza.';
    if ($username === '') $errors[] = 'Nazwa użytkownika jest wymagana.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Podaj poprawny e-mail.';
    if (strlen($password) < 6) $errors[] = 'Hasło musi mieć co najmniej 6 znaków.';
    if ($password !== $password2) $errors[] = 'Hasła nie są takie same.';

    if (!$errors) {
        if (register_user($username, $email, $password)) {
            flash('success', 'Konto utworzone. Zaloguj się.');
            header('Location: ' . base_url('login.php'));
            exit;
        } else {
            $errors[] = 'Nie udało się utworzyć konta. Nazwa lub e-mail zajęte?';
        }
    }
}
?>
<h1 class="h3 mb-3">Rejestracja</h1>
<?php foreach ($errors as $e): ?>
  <div class="alert alert-danger"><?php echo htmlspecialchars($e); ?></div>
<?php endforeach; ?>
<form method="post" class="row gy-3">
  <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
  <div class="col-12">
    <label class="form-label">Nazwa użytkownika</label>
    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" maxlength="50" required>
  </div>
  <div class="col-12">
    <label class="form-label">E-mail</label>
    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
  </div>
  <div class="col-md-6">
    <label class="form-label">Hasło</label>
    <input type="password" name="password" class="form-control" required>
  </div>
  <div class="col-md-6">
    <label class="form-label">Powtórz hasło</label>
    <input type="password" name="password2" class="form-control" required>
  </div>
  <div class="col-12">
    <button class="btn btn-warning" type="submit">Utwórz konto</button>
    <a class="btn btn-secondary" href="<?php echo htmlspecialchars(base_url('index.php')); ?>">Anuluj</a>
  </div>
</form>
<?php require_once __DIR__ . '/../inc/footer.php'; ?>
