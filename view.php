<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$postId = (int) ($_GET['id'] ?? 0);
$post = findPostForUser($postId, (int) currentUserId());

if (!$post) {
    setFlash('warning', 'Post topilmadi.');
    redirect('/index.php');
}

$author = findUserById((int) $post['user_id']);

$pageTitle = (string) $post['title'];

require_once __DIR__ . '/includes/header.php';
?>

<article class="card app-card article-shell mx-auto">
    <div class="card-body p-4 p-lg-5">
        <div class="d-flex justify-content-between align-items-start gap-3 mb-4 flex-wrap">
            <div>
                <span class="eyebrow"><?= e($post['category']) ?></span>
                <h1 class="display-6 fw-bold mb-2"><?= e($post['title']) ?></h1>
                <div class="text-secondary small">
                    Muallif: <?= e(($author['first_name'] ?? '') . ' ' . ($author['last_name'] ?? '')) ?>
                    • <?= e(date('d.m.Y H:i', strtotime($post['created_at']))) ?>
                    • <span class="badge text-bg-<?= $post['status'] === 'published' ? 'success' : 'secondary' ?>"><?= e(ucfirst($post['status'])) ?></span>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="/update.php?id=<?= (int) $post['id'] ?>" class="btn btn-outline-dark">Tahrirlash</a>
                <a href="/index.php" class="btn btn-outline-secondary">Orqaga</a>
            </div>
        </div>
        <div class="article-content"><?= nl2br(e($post['content'])) ?></div>
    </div>
</article>

<?php require_once __DIR__ . '/includes/footer.php'; ?>