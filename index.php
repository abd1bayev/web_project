<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$pageTitle = 'Dashboard';
$search = trim((string) ($_GET['search'] ?? ''));
$category = trim((string) ($_GET['category'] ?? ''));
$status = trim((string) ($_GET['status'] ?? ''));
$allowedSorts = ['created_at', 'title', 'category', 'status'];
$sort = in_array($_GET['sort'] ?? 'created_at', $allowedSorts, true) ? (string) $_GET['sort'] : 'created_at';
$direction = strtolower((string) ($_GET['direction'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';
$page = normalizePage((int) ($_GET['page'] ?? 1));
$perPage = 6;
$filteredPosts = searchPostsForUser((int) currentUserId(), $search, $category, $status, $sort, $direction);
$totalItems = count($filteredPosts);
$totalPages = max(1, (int) ceil($totalItems / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;
$posts = array_slice($filteredPosts, $offset, $perPage);

$categories = distinctCategoriesForUser((int) currentUserId());

function sortDirection(string $column, string $currentSort, string $currentDirection): string
{
    if ($column !== $currentSort) {
        return 'asc';
    }

    return $currentDirection === 'asc' ? 'desc' : 'asc';
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="hero-panel mb-4 mb-lg-5">
    <div>
        <span class="eyebrow">Professional PHP Workspace</span>
        <h1 class="display-6 fw-bold mb-2">Postlaringizni bir joyda boshqaring</h1>
        <p class="text-secondary mb-0">Qidiruv, saralash, sahifalash va xavfsiz boshqaruv bilan to'liq CRUD panel.</p>
    </div>
    <a href="/create.php" class="btn btn-primary btn-lg">Yangi post qo'shish</a>
</section>

<section class="card app-card mb-4">
    <div class="card-body">
        <form class="row g-3" method="get">
            <div class="col-lg-4">
                <label class="form-label">Qidiruv</label>
                <input type="text" class="form-control" name="search" value="<?= e($search) ?>" placeholder="Sarlavha yoki matn bo'yicha">
            </div>
            <div class="col-lg-2">
                <label class="form-label">Kategoriya</label>
                <select class="form-select" name="category">
                    <option value="">Barchasi</option>
                    <?php foreach ($categories as $categoryOption): ?>
                        <option value="<?= e((string) $categoryOption) ?>" <?= $category === $categoryOption ? 'selected' : '' ?>><?= e((string) $categoryOption) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-2">
                <label class="form-label">Holat</label>
                <select class="form-select" name="status">
                    <option value="">Barchasi</option>
                    <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Published</option>
                </select>
            </div>
            <div class="col-lg-2">
                <label class="form-label">Saralash</label>
                <select class="form-select" name="sort">
                    <option value="created_at" <?= $sort === 'created_at' ? 'selected' : '' ?>>Sana</option>
                    <option value="title" <?= $sort === 'title' ? 'selected' : '' ?>>Sarlavha</option>
                    <option value="category" <?= $sort === 'category' ? 'selected' : '' ?>>Kategoriya</option>
                    <option value="status" <?= $sort === 'status' ? 'selected' : '' ?>>Holat</option>
                </select>
            </div>
            <div class="col-lg-2">
                <label class="form-label">Yo'nalish</label>
                <select class="form-select" name="direction">
                    <option value="desc" <?= $direction === 'desc' ? 'selected' : '' ?>>DESC</option>
                    <option value="asc" <?= $direction === 'asc' ? 'selected' : '' ?>>ASC</option>
                </select>
            </div>
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary" type="submit">Filtrlash</button>
                <a class="btn btn-outline-secondary" href="/index.php">Tozalash</a>
            </div>
        </form>
    </div>
</section>

<?php if (!$posts): ?>
    <div class="empty-state card app-card text-center p-5">
        <h2 class="h4 mb-2">Natija topilmadi</h2>
        <p class="text-secondary mb-3">Hozircha post yo'q yoki filter bo'yicha mos yozuv topilmadi.</p>
        <a href="/create.php" class="btn btn-primary">Birinchi postni yarating</a>
    </div>
<?php else: ?>
    <div class="table-responsive app-card mb-4">
        <table class="table table-hover align-middle mb-0">
            <thead>
            <tr>
                <th><a href="?<?= e(buildQueryString(['search' => $search, 'category' => $category, 'status' => $status, 'sort' => 'title', 'direction' => sortDirection('title', $sort, $direction)])) ?>">Sarlavha</a></th>
                <th><a href="?<?= e(buildQueryString(['search' => $search, 'category' => $category, 'status' => $status, 'sort' => 'category', 'direction' => sortDirection('category', $sort, $direction)])) ?>">Kategoriya</a></th>
                <th><a href="?<?= e(buildQueryString(['search' => $search, 'category' => $category, 'status' => $status, 'sort' => 'status', 'direction' => sortDirection('status', $sort, $direction)])) ?>">Holat</a></th>
                <th><a href="?<?= e(buildQueryString(['search' => $search, 'category' => $category, 'status' => $status, 'sort' => 'created_at', 'direction' => sortDirection('created_at', $sort, $direction)])) ?>">Sana</a></th>
                <th class="text-end">Amallar</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($posts as $post): ?>
                <tr>
                    <td>
                        <div class="fw-semibold"><?= e($post['title']) ?></div>
                        <div class="small text-secondary"><?= e(excerpt($post['content'])) ?></div>
                    </td>
                    <td><?= e($post['category']) ?></td>
                    <td><span class="badge text-bg-<?= $post['status'] === 'published' ? 'success' : 'secondary' ?>"><?= e(ucfirst($post['status'])) ?></span></td>
                    <td><?= e(date('d.m.Y H:i', strtotime($post['created_at']))) ?></td>
                    <td class="text-end">
                        <div class="d-inline-flex gap-2">
                            <a class="btn btn-sm btn-outline-primary" href="/view.php?id=<?= (int) $post['id'] ?>">Ko'rish</a>
                            <a class="btn btn-sm btn-outline-dark" href="/update.php?id=<?= (int) $post['id'] ?>">Tahrirlash</a>
                            <a class="btn btn-sm btn-outline-danger" href="/delete.php?id=<?= (int) $post['id'] ?>&token=<?= e(csrfToken()) ?>" data-confirm-delete="true">O'chirish</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav>
            <ul class="pagination">
                <?php foreach (paginationRange($page, $totalPages) as $pageNumber): ?>
                    <li class="page-item <?= $pageNumber === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= e(buildQueryString(['search' => $search, 'category' => $category, 'status' => $status, 'sort' => $sort, 'direction' => $direction, 'page' => $pageNumber])) ?>"><?= $pageNumber ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    <?php endif; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>