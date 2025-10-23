<?php
require_once __DIR__ . '/../inc/header.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT p.id, p.title, p.content, p.created_at, p.updated_at, p.user_id, u.username 
                     FROM posts p JOIN users u ON u.id = p.user_id WHERE p.id = :id");
$stmt->execute([':id' => $id]);
$post = $stmt->fetch();
if (!$post) {
    http_response_code(404);
    echo "<p>Nie znaleziono wpisu.</p>";
    require_once __DIR__ . '/../inc/footer.php';
    exit;
}
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 m-0"><?php echo htmlspecialchars($post['title']); ?></h1>
  <div class="d-flex gap-2">
    <?php if (is_logged_in() && (is_admin() || current_user()['id'] == $post['user_id'])): ?>
      <a class="btn btn-outline-primary btn-sm" href="<?php echo htmlspecialchars(base_url('post_edit.php?id=' . $post['id'])); ?>">Edytuj</a>
      <form method="post" action="<?php echo htmlspecialchars(base_url('post_delete.php')); ?>" class="d-inline">
        <input type="hidden" name="id" value="<?php echo (int)$post['id']; ?>">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Usunąć ten wpis?');">Usuń</button>
      </form>
    <?php endif; ?>
  </div>
</div>
<p class="text-muted small mb-2">
  Autor: <?php echo htmlspecialchars($post['username']); ?> • 
  Dodano: <?php echo htmlspecialchars($post['created_at']); ?>
  <?php if ($post['updated_at']): ?> • Edytowano: <?php echo htmlspecialchars($post['updated_at']); ?><?php endif; ?>
</p>
<div class="card"><div class="card-body">
  <?php echo nl2br(htmlspecialchars($post['content'])); ?>
</div></div>

<p class="mt-3"><a href="<?php echo htmlspecialchars(base_url('index.php')); ?>" class="btn btn-secondary btn-sm">Wróć</a></p>
<?php require_once __DIR__ . '/../inc/footer.php'; ?>
