<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$postId = (int) ($_GET['id'] ?? 0);
$token = $_GET['token'] ?? null;

if ($postId < 1 || !verifyCsrfToken($token)) {
    setFlash('danger', 'O\'chirish so\'rovi rad etildi.');
    redirect('/index.php');
}

$deleted = deletePostRecord($postId, (int) currentUserId());

setFlash($deleted ? 'success' : 'warning', $deleted ? 'Post o\'chirildi.' : 'Post topilmadi.');
redirect('/index.php');