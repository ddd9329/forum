<?php
require_once __DIR__ . '/../inc/header.php';
require_login();

$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf'] ?? '')) $errors[] = 'Błędny token formularza.';
    if ($title === '') $errors[] = 'Tytuł jest wymagany.';
    if ($content === '') $errors[] = 'Treść jest wymagana.';
    if (!$errors) {
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content) VALUES (:uid, :t, :c)");
        $stmt->execute([':uid' => current_user()['id'], ':t' => $title, ':c' => $content]);
        flash('success', 'Wpis dodany.');
        header('Location: ' . base_url('index.php'));
        exit;
    }
}
?>
<h1 class="h3 mb-3">Nowy wpis</h1>
<?php foreach ($errors as $e): ?>
  <div class="alert alert-danger"><?php echo htmlspecialchars($e); ?></div>
<?php endforeach; ?>
<form method="post">
  <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
  <div class="mb-3">
    <label class="form-label">Tytuł</label>
    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($title); ?>" maxlength="255" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Treść</label>
    <textarea name="content" class="form-control" rows="6" required><?php echo htmlspecialchars($content); ?></textarea>
  </div>
  <button class="btn btn-primary" type="submit">Zapisz</button>
  <a class="btn btn-secondary" href="<?php echo htmlspecialchars(base_url('index.php')); ?>">Anuluj</a>
</form>
<?php require_once __DIR__ . '/../inc/footer.php'; ?>
