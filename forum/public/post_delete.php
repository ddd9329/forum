<?php
require_once __DIR__ . '/../inc/header.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method Not Allowed";
    require_once __DIR__ . '/../inc/footer.php';
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$token = $_POST['csrf'] ?? '';

if (!csrf_validate($token)) {
    flash('danger', 'Błędny token formularza.');
    header('Location: ' . base_url('index.php'));
    exit;
}

$stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = :id");
$stmt->execute([':id' => $id]);
$post = $stmt->fetch();

if (!$post) {
    flash('warning', 'Wpis nie istnieje.');
    header('Location: ' . base_url('index.php'));
    exit;
}

if (!is_admin() && current_user()['id'] != $post['user_id']) {
    http_response_code(403);
    echo "<p>Brak uprawnień.</p>";
    require_once __DIR__ . '/../inc/footer.php';
    exit;
}

$del = $pdo->prepare("DELETE FROM posts WHERE id = :id");
$del->execute([':id' => $id]);

flash('success', 'Wpis usunięty.');
header('Location: ' . base_url('index.php'));
exit;
