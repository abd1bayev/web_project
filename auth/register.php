<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    redirect('/index.php');
}

$pageTitle = "Ro'yxatdan o'tish";
$errors = [];
$form = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'bio' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = "So'rov xavfsizlik tekshiruvdan o'tmadi.";
    }

    foreach ($form as $key => $value) {
        $form[$key] = old($_POST, $key);
    }
    $form['email'] = strLower($form['email']);

    $password = (string) ($_POST['password'] ?? '');
    $passwordConfirmation = (string) ($_POST['password_confirmation'] ?? '');

    if ($form['first_name'] === '' || $form['last_name'] === '') {
        $errors[] = 'Ism va familiya majburiy.';
    }
    if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email manzil noto\'g\'ri.';
    }
    if (strLength($password) < 6) {
        $errors[] = 'Parol kamida 6 belgidan iborat bo\'lishi kerak.';
    }
    if ($password !== $passwordConfirmation) {
        $errors[] = 'Parollar mos kelmadi.';
    }

    if (!$errors) {
        if (userEmailExists($form['email'])) {
            $errors[] = 'Bu email allaqachon ro\'yxatdan o\'tgan.';
        } else {
            createUser([
                'first_name' => $form['first_name'],
                'last_name' => $form['last_name'],
                'email' => $form['email'],
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'bio' => $form['bio'],
            ]);

            setFlash('success', "Ro'yxatdan o'tish muvaffaqiyatli yakunlandi.");
            redirect('/auth/login.php');
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<section class="auth-shell mx-auto">
    <div class="card app-card">
        <div class="card-body p-4 p-lg-5">
            <span class="eyebrow">Registration</span>
            <h1 class="h3 mb-2">Yangi akkaunt yaratish</h1>
            <p class="text-secondary mb-4">Shaxsiy profil va boshqaruv paneliga kirish uchun ma'lumotlarni kiriting.</p>

            <?php if ($errors): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <div><?= e($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

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
                    <textarea name="bio" rows="3" class="form-control" placeholder="O'zingiz haqingizda qisqacha yozing"><?= e($form['bio']) ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Parol</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Parolni tasdiqlang</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <div class="col-12 d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">Ro'yxatdan o'tish</button>
                </div>
            </form>
            <p class="mt-4 mb-0 text-secondary">Akkauntingiz bormi? <a href="/auth/login.php">Kiring</a></p>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
