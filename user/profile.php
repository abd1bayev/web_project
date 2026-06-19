<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

$user = findUserById($pdo, (int) currentUserId());
if (!$user) {
    setFlash('danger', 'Foydalanuvchi topilmadi.');
    redirect('/auth/logout.php');
}

$stats = userPostStats((int) currentUserId());

$pageTitle = 'Profil';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="row g-4">
    <div class="col-lg-4">
        <div class="card app-card h-100">
            <div class="card-body p-4">
                <span class="eyebrow">User profile</span>
                <h1 class="h3 mb-1"><?= e($user['first_name'] . ' ' . $user['last_name']) ?></h1>
                <p class="text-secondary mb-3"><?= e($user['email']) ?></p>
                <p class="mb-4"><?= e($user['bio'] ?: 'Bio kiritilmagan.') ?></p>
                <div class="small text-secondary mb-4">Ro'yxatdan o'tgan sana: <?= e(date('d.m.Y', strtotime($user['created_at']))) ?></div>
                <a class="btn btn-primary w-100" href="/user/edit_profile.php">Profilni tahrirlash</a>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card metric-card h-100">
                    <div class="card-body">
                        <div class="metric-label">Jami postlar</div>
                        <div class="metric-value"><?= (int) $stats['total_posts'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card metric-card h-100">
                    <div class="card-body">
                        <div class="metric-label">Published postlar</div>
                        <div class="metric-value"><?= (int) $stats['published_posts'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card app-card">
                    <div class="card-body p-4">
                        <h2 class="h4 mb-3">Hisob sozlamalari</h2>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item px-0 d-flex justify-content-between"><span>To'liq ism</span><strong><?= e($user['first_name'] . ' ' . $user['last_name']) ?></strong></li>
                            <li class="list-group-item px-0 d-flex justify-content-between"><span>Email</span><strong><?= e($user['email']) ?></strong></li>
                            <li class="list-group-item px-0 d-flex justify-content-between"><span>Bio</span><strong class="text-end ms-3"><?= e($user['bio'] ?: 'Mavjud emas') ?></strong></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
