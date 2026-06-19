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

$pageTitle = 'Profilni tahrirlash';
$errors = [];
$form = [
    'first_name' => (string) $user['first_name'],
    'last_name' => (string) $user['last_name'],
    'email' => (string) $user['email'],
    'bio' => (string) ($user['bio'] ?? ''),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = "So'rov xavfsizlik tekshiruvdan o'tmadi.";
    }

    foreach ($form as $key => $value) {
        $form[$key] = old($_POST, $key);
    }
    $form['email'] = strLower($form['email']);
    $currentPassword = (string) ($_POST['current_password'] ?? '');
    $newPassword = (string) ($_POST['new_password'] ?? '');
    $newPasswordConfirmation = (string) ($_POST['new_password_confirmation'] ?? '');

    if ($form['first_name'] === '' || $form['last_name'] === '') {
        $errors[] = 'Ism va familiya majburiy.';
    }
    if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email manzil noto\'g\'ri.';
    }

    if (userEmailExists($form['email'], (int) currentUserId())) {
        $errors[] = 'Bu email boshqa foydalanuvchi tomonidan ishlatilmoqda.';
    }

    $passwordHash = null;
    if ($newPassword !== '' || $newPasswordConfirmation !== '' || $currentPassword !== '') {
        $currentUser = findUserById((int) currentUserId());

        if (!$currentUser || !password_verify($currentPassword, $currentUser['password_hash'])) {
            $errors[] = 'Joriy parol noto\'g\'ri.';
        }
        if (strLength($newPassword) < 6) {
            $errors[] = 'Yangi parol kamida 6 belgidan iborat bo\'lishi kerak.';
        }
        if ($newPassword !== $newPasswordConfirmation) {
            $errors[] = 'Yangi parollar mos kelmadi.';
        }

        if (!$errors) {
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        }
    }

    if (!$errors) {
        $attributes = [
            'first_name' => $form['first_name'],
            'last_name' => $form['last_name'],
            'email' => $form['email'],
            'bio' => $form['bio'],
        ];

        if ($passwordHash !== null) {
            $attributes['password_hash'] = $passwordHash;
        }

        updateUserRecord((int) currentUserId(), $attributes);
        $_SESSION['user_name'] = $form['first_name'] . ' ' . $form['last_name'];

        setFlash('success', 'Profil muvaffaqiyatli yangilandi.');
        redirect('/user/profile.php');
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<section class="form-shell mx-auto">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="eyebrow">Account settings</span>
            <h1 class="h3 mb-1">Profilni yangilash</h1>
            <p class="text-secondary mb-0">Shaxsiy ma'lumotlar va parolni xavfsiz tarzda yangilang.</p>
        </div>
        <a href="/user/profile.php" class="btn btn-outline-secondary">Profilga qaytish</a>
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
                <div class="col-md-6">
                    <label class="form-label">Ism</label>
                    <input type="text" name="first_name" class="form-control" value="<?= e($form['first_name']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Familiya</label>
                    <input type="text" name="last_name" class="form-control" value="<?= e($form['last_name']) ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= e($form['email']) ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Bio</label>
                    <textarea name="bio" rows="4" class="form-control"><?= e($form['bio']) ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Joriy parol</label>
                    <input type="password" name="current_password" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Yangi parol</label>
                    <input type="password" name="new_password" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Yangi parol tasdig'i</label>
                    <input type="password" name="new_password_confirmation" class="form-control">
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Saqlash</button>
                </div>
            </form>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
