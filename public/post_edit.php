<?php
require_once __DIR__ . '/../inc/header.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :id");
$stmt->execute([':id' => $id]);
$post = $stmt->fetch();
if (!$post) {
    http_response_code(404);
    echo "<p>Nie znaleziono wpisu.</p>";
    require_once __DIR__ . '/../inc/footer.php';
    exit;
}

if (!is_admin() && current_user()['id'] != $post['user_id']) {
    http_response_code(403);
    echo "<p>Brak uprawnień.</p>";
    require_once __DIR__ . '/../inc/footer.php';
    exit;
}

$title = $_SERVER['REQUEST_METHOD'] === 'POST' ? trim($_POST['title'] ?? '') : $post['title'];
$content = $_SERVER['REQUEST_METHOD'] === 'POST' ? trim($_POST['content'] ?? '') : $post['content'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf'] ?? '')) $errors[] = 'Błędny token formularza.';
    if ($title === '') $errors[] = 'Tytuł jest wymagany.';
    if ($content === '') $errors[] = 'Treść jest wymagana.';
    if (!$errors) {
        $stmt = $pdo->prepare("UPDATE posts SET title = :t, content = :c WHERE id = :id");
        $stmt->execute([':t' => $title, ':c' => $content, ':id' => $id]);
        flash('success', 'Zapisano zmiany.');
        header('Location: ' . base_url('post_view.php?id=' . $id));
        exit;
    }
}
?>
<h1 class="h3 mb-3">Edycja wpisu</h1>
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
  <a class="btn btn-secondary" href="<?php echo htmlspecialchars(base_url('post_view.php?id=' . $id)); ?>">Anuluj</a>
</form>
<?php require_once __DIR__ . '/../inc/footer.php'; ?>
