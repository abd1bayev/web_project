<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$storageDir = __DIR__ . '/../storage';
$usersFile = $storageDir . '/users.json';
$postsFile = $storageDir . '/posts.json';

if (!is_dir($storageDir) && !mkdir($storageDir, 0777, true) && !is_dir($storageDir)) {
    exit("Storage katalogini yaratib bo'lmadi.");
}

if (!file_exists($usersFile)) {
    file_put_contents($usersFile, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

if (!file_exists($postsFile)) {
    file_put_contents($postsFile, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$pdo = null;