<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$pageTitle = 'Yangi post';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = "So'rov xavfsizlik tekshiruvdan o'tmadi.";
    }

    $title = old($_POST, 'title');
    $category = old($_POST, 'category');
    $status = old($_POST, 'status', 'draft');
    $content = old($_POST, 'content');

    if ($title === '') {
        $errors[] = 'Sarlavha majburiy.';
    }
    if ($category === '') {
        $errors[] = 'Kategoriya majburiy.';
    }
    if (!in_array($status, ['draft', 'published'], true)) {
        $errors[] = "Holat noto'g'ri.";
    }
    if ($content === '') {
        $errors[] = 'Post matni majburiy.';
    }

    if (!$errors) {
        createPostRecord([
            'user_id' => currentUserId(),
            'title' => $title,
            'category' => $category,
            'status' => $status,
            'content' => $content,
        ]);

        setFlash('success', 'Post muvaffaqiyatli yaratildi.');
        redirect('/index.php');
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="form-shell mx-auto">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="eyebrow">Create</span>
            <h1 class="h3 mb-1">Yangi post yarating</h1>
            <p class="text-secondary mb-0">Kategoriyasi, holati va kontenti bilan to'liq yozuv qo'shing.</p>
        </div>
        <a href="/index.php" class="btn btn-outline-secondary">Orqaga</a>
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
                <div class="col-md-8">
                    <label class="form-label">Sarlavha</label>
                    <input type="text" name="title" class="form-control" value="<?= e($title ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kategoriya</label>
                    <input type="text" name="category" class="form-control" value="<?= e($category ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Holat</label>
                    <select name="status" class="form-select">
                        <option value="draft" <?= ($status ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="published" <?= ($status ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Kontent</label>
                    <textarea name="content" rows="10" class="form-control" required><?= e($content ?? '') ?></textarea>
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Saqlash</button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>