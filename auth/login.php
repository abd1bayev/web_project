<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    redirect('/index.php');
}

$pageTitle = 'Kirish';
$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = "So'rov xavfsizlik tekshiruvdan o'tmadi.";
    }

    $email = strLower(old($_POST, 'email'));
    $password = (string) ($_POST['password'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email manzil noto\'g\'ri.';
    }
    if ($password === '') {
        $errors[] = 'Parol majburiy.';
    }

    if (!$errors) {
        $user = findUserByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $errors[] = 'Email yoki parol noto\'g\'ri.';
        } else {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            setFlash('success', 'Xush kelibsiz, ' . $_SESSION['user_name'] . '.');
            redirect('/index.php');
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<section class="auth-shell mx-auto">
    <div class="card app-card">
        <div class="card-body p-4 p-lg-5">
            <span class="eyebrow">Authentication</span>
            <h1 class="h3 mb-2">Tizimga kirish</h1>
            <p class="text-secondary mb-4">Akkauntingiz orqali postlar va profilingizni boshqaring.</p>

            <?php if ($errors): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <div><?= e($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" class="row g-3">
                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                <div class="col-12">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= e($email) ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Parol</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-12 d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">Kirish</button>
                </div>
            </form>
            <p class="mt-4 mb-0 text-secondary">Akkauntingiz yo'qmi? <a href="/auth/register.php">Ro'yxatdan o'ting</a></p>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
