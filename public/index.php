<?php
require_once __DIR__ . '/../inc/header.php';

$stmt = $pdo->query("SELECT p.id, p.title, p.content, p.created_at, p.updated_at, p.user_id, u.username 
                     FROM posts p JOIN users u ON u.id = p.user_id
                     ORDER BY p.created_at DESC");
$posts = $stmt->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 m-0">Wpisy</h1>
  <?php if (is_logged_in()): ?>
    <a href="<?php echo htmlspecialchars(base_url('post_create.php')); ?>" class="btn btn-primary">Dodaj wpis</a>
  <?php endif; ?>
</div>

<?php if (!$posts): ?>
  <p>Brak wpisów.</p>
<?php endif; ?>

<?php foreach ($posts as $post): ?>
  <div class="card mb-3">
    <div class="card-body">
      <h2 class="h5 card-title"><?php echo htmlspecialchars($post['title']); ?></h2>
      <p class="card-text"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
      <p class="text-muted small mb-2">
        Autor: <?php echo htmlspecialchars($post['username']); ?> • 
        Dodano: <?php echo htmlspecialchars($post['created_at']); ?>
        <?php if ($post['updated_at']): ?> • Edytowano: <?php echo htmlspecialchars($post['updated_at']); ?><?php endif; ?>
      </p>
      <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary btn-sm" href="<?php echo htmlspecialchars(base_url('post_view.php?id=' . $post['id'])); ?>">Podgląd</a>
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
  </div>
<?php endforeach; ?>

<?php require_once __DIR__ . '/../inc/footer.php'; ?>
