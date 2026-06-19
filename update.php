<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$postId = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$post = findPostForUser($postId, (int) currentUserId());

if (!$post) {
    setFlash('warning', 'Post topilmadi.');
    redirect('/index.php');
}

$pageTitle = 'Postni tahrirlash';
$errors = [];
$form = [
    'title' => (string) $post['title'],
    'category' => (string) $post['category'],
    'status' => (string) $post['status'],
    'content' => (string) $post['content'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = "So'rov xavfsizlik tekshiruvdan o'tmadi.";
    }

    foreach ($form as $key => $value) {
        $form[$key] = old($_POST, $key);
    }

    if ($form['title'] === '') {
        $errors[] = 'Sarlavha majburiy.';
    }
    if ($form['category'] === '') {
        $errors[] = 'Kategoriya majburiy.';
    }
    if (!in_array($form['status'], ['draft', 'published'], true)) {
        $errors[] = 'Holat noto\'g\'ri.';
    }
    if ($form['content'] === '') {
        $errors[] = 'Kontent majburiy.';
    }

    if (!$errors) {
        updatePostRecord($postId, (int) currentUserId(), [
            'title' => $form['title'],
            'category' => $form['category'],
            'status' => $form['status'],
            'content' => $form['content'],
        ]);

        setFlash('success', 'Post yangilandi.');
        redirect('/view.php?id=' . $postId);
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="form-shell mx-auto">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="eyebrow">Update</span>
            <h1 class="h3 mb-1">Postni tahrirlash</h1>
            <p class="text-secondary mb-0">Mavjud yozuvni to'liq boshqaring.</p>
        </div>
        <a href="/view.php?id=<?= $postId ?>" class="btn btn-outline-secondary">Bekor qilish</a>
    </div>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <div><?= e($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="card app-card">
        <div class="card-body p-4">
            <form method="post" class="row g-3">
                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                <input type="hidden" name="id" value="<?= $postId ?>">
                <div class="col-md-8">
                    <label class="form-label">Sarlavha</label>
                    <input type="text" name="title" class="form-control" value="<?= e($form['title']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kategoriya</label>
                    <input type="text" name="category" class="form-control" value="<?= e($form['category']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Holat</label>
                    <select name="status" class="form-select">
                        <option value="draft" <?= $form['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="published" <?= $form['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Kontent</label>
                    <textarea name="content" rows="10" class="form-control" required><?= e($form['content']) ?></textarea>
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Yangilash</button>
                </div>
            </form>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
