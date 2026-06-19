<?php
declare(strict_types=1);

function strLower(string $value): string
{
    return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
}

function strLength(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

function strSlice(string $value, int $start, ?int $length = null): string
{
    if (function_exists('mb_substr')) {
        return $length === null
            ? mb_substr($value, $start, null, 'UTF-8')
            : mb_substr($value, $start, $length, 'UTF-8');
    }

    return $length === null ? substr($value, $start) : substr($value, $start, $length);
}

function strContainsAt(string $haystack, string $needle): int|false
{
    return function_exists('mb_strpos') ? mb_strpos($haystack, $needle, 0, 'UTF-8') : strpos($haystack, $needle);
}

function storagePath(string $name): string
{
    return __DIR__ . '/../storage/' . $name . '.json';
}

function readStorage(string $name): array
{
    $path = storagePath($name);

    if (!file_exists($path)) {
        return [];
    }

    $content = file_get_contents($path);
    if ($content === false || $content === '') {
        return [];
    }

    $decoded = json_decode($content, true);

    return is_array($decoded) ? $decoded : [];
}

function writeStorage(string $name, array $records): void
{
    $path = storagePath($name);
    $json = json_encode(array_values($records), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    if ($json === false || file_put_contents($path, $json, LOCK_EX) === false) {
        throw new RuntimeException('Storage faylini saqlab bo\'lmadi.');
    }
}

function nextId(array $records): int
{
    $ids = array_map(static fn (array $record): int => (int) ($record['id'] ?? 0), $records);

    return $ids ? max($ids) + 1 : 1;
}

function allUsers(): array
{
    return readStorage('users');
}

function saveUsers(array $users): void
{
    writeStorage('users', $users);
}

function allPosts(): array
{
    return readStorage('posts');
}

function savePosts(array $posts): void
{
    writeStorage('posts', $posts);
}

function nowTimestamp(): string
{
    return date('Y-m-d H:i:s');
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        setFlash('error', 'Davom etish uchun tizimga kiring.');
        redirect('/auth/login.php');
    }
}

function currentUserId(): ?int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function getFlash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(?string $token): bool
{
    return isset($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
}

function old(array $source, string $key, string $default = ''): string
{
    return isset($source[$key]) ? trim((string) $source[$key]) : $default;
}

function normalizePage(int $page): int
{
    return max(1, $page);
}

function excerpt(string $text, int $length = 140): string
{
    if (strLength($text) <= $length) {
        return $text;
    }

    return strSlice($text, 0, $length - 3) . '...';
}

function paginationRange(int $page, int $totalPages): array
{
    if ($totalPages < 1) {
        return [];
    }

    $start = max(1, $page - 2);
    $end = min($totalPages, $page + 2);

    return range($start, $end);
}

function buildQueryString(array $params): string
{
    return http_build_query(array_filter($params, static fn ($value) => $value !== '' && $value !== null));
}

function findUserById($pdoOrUserId, ?int $userId = null): ?array
{
    $targetId = $userId ?? (int) $pdoOrUserId;

    foreach (allUsers() as $user) {
        if ((int) $user['id'] === $targetId) {
            return $user;
        }
    }

    return null;
}

function findUserByEmail(string $email): ?array
{
    $normalizedEmail = strLower(trim($email));

    foreach (allUsers() as $user) {
        if (strLower((string) $user['email']) === $normalizedEmail) {
            return $user;
        }
    }

    return null;
}

function createUser(array $attributes): array
{
    $users = allUsers();
    $timestamp = nowTimestamp();
    $user = [
        'id' => nextId($users),
        'first_name' => $attributes['first_name'],
        'last_name' => $attributes['last_name'],
        'email' => strLower($attributes['email']),
        'password_hash' => $attributes['password_hash'],
        'bio' => $attributes['bio'] ?? '',
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
    ];

    $users[] = $user;
    saveUsers($users);

    return $user;
}

function updateUserRecord(int $userId, array $attributes): ?array
{
    $users = allUsers();

    foreach ($users as $index => $user) {
        if ((int) $user['id'] !== $userId) {
            continue;
        }

        $users[$index] = array_merge($user, $attributes, ['updated_at' => nowTimestamp()]);
        saveUsers($users);

        return $users[$index];
    }

    return null;
}

function userEmailExists(string $email, ?int $exceptUserId = null): bool
{
    $normalizedEmail = strLower(trim($email));

    foreach (allUsers() as $user) {
        if ($exceptUserId !== null && (int) $user['id'] === $exceptUserId) {
            continue;
        }

        if (strLower((string) $user['email']) === $normalizedEmail) {
            return true;
        }
    }

    return false;
}

function postsForUser(int $userId): array
{
    return array_values(array_filter(allPosts(), static fn (array $post): bool => (int) $post['user_id'] === $userId));
}

function findPostForUser(int $postId, int $userId): ?array
{
    foreach (allPosts() as $post) {
        if ((int) $post['id'] === $postId && (int) $post['user_id'] === $userId) {
            return $post;
        }
    }

    return null;
}

function createPostRecord(array $attributes): array
{
    $posts = allPosts();
    $timestamp = nowTimestamp();
    $post = [
        'id' => nextId($posts),
        'user_id' => (int) $attributes['user_id'],
        'title' => $attributes['title'],
        'category' => $attributes['category'],
        'status' => $attributes['status'],
        'content' => $attributes['content'],
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
    ];

    $posts[] = $post;
    savePosts($posts);

    return $post;
}

function updatePostRecord(int $postId, int $userId, array $attributes): ?array
{
    $posts = allPosts();

    foreach ($posts as $index => $post) {
        if ((int) $post['id'] !== $postId || (int) $post['user_id'] !== $userId) {
            continue;
        }

        $posts[$index] = array_merge($post, $attributes, ['updated_at' => nowTimestamp()]);
        savePosts($posts);

        return $posts[$index];
    }

    return null;
}

function deletePostRecord(int $postId, int $userId): bool
{
    $posts = allPosts();
    $filtered = array_values(array_filter($posts, static fn (array $post): bool => !((int) $post['id'] === $postId && (int) $post['user_id'] === $userId)));

    if (count($filtered) === count($posts)) {
        return false;
    }

    savePosts($filtered);

    return true;
}

function searchPostsForUser(int $userId, string $search, string $category, string $status, string $sort, string $direction): array
{
    $posts = postsForUser($userId);

    $posts = array_values(array_filter($posts, static function (array $post) use ($search, $category, $status): bool {
        if ($search !== '') {
            $needle = strLower($search);
            $haystack = strLower($post['title'] . ' ' . $post['content']);
            if (strContainsAt($haystack, $needle) === false) {
                return false;
            }
        }

        if ($category !== '' && $post['category'] !== $category) {
            return false;
        }

        if ($status !== '' && $post['status'] !== $status) {
            return false;
        }

        return true;
    }));

    usort($posts, static function (array $left, array $right) use ($sort, $direction): int {
        $leftValue = $left[$sort] ?? '';
        $rightValue = $right[$sort] ?? '';
        $result = $leftValue <=> $rightValue;

        return $direction === 'asc' ? $result : -$result;
    });

    return $posts;
}

function distinctCategoriesForUser(int $userId): array
{
    $categories = array_map(static fn (array $post): string => (string) $post['category'], postsForUser($userId));
    $categories = array_values(array_unique($categories));
    sort($categories);

    return $categories;
}

function userPostStats(int $userId): array
{
    $posts = postsForUser($userId);
    $published = array_filter($posts, static fn (array $post): bool => $post['status'] === 'published');

    return [
        'total_posts' => count($posts),
        'published_posts' => count($published),
    ];
}