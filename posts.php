<?php
require __DIR__ . '/vendor/autoload.php';
/*require __DIR__ . '/functions/config.php';
require __DIR__ . '/functions/categories.php';
require __DIR__ . '/functions/posts.php';*/

use function CompanyName\Blog\getPosts;
use function CompanyName\Blog\redirectToError;
use function CompanyName\Blog\deletePost;

const STATUSES = [
    'ok' => 'Пост успешно удален',
];
$success = STATUSES[($_GET['success'] ?? '')] ?? '';

try {
    $posts = getPosts();

    // D - Delete
    if (isset($_GET['action']) && $_GET['action'] === 'delete') {

        $id = (int) ($_GET['id'] ?? null);
        deletePost($id);
        header("Location: /posts.php?success=ok");
        die();
    }
} catch (Exception $e) {
    $errorId = 'ERR_' . date('Ymd_His') . '_' . uniqid();

    $errorDetails = [
        'message' => $e->getMessage(),
        'errorId' => $errorId,
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];
    error_log(json_encode($errorDetails, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    //Редирект
    redirectToError(500, $e->getMessage(), $errorId);
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Главная</title>
    <link rel="stylesheet" href="/css/style.css">
</head>

<body>
    <?php include __DIR__ . '/components/menu.php' ?>
    <a href="/post-create.php"> <button class="createBtn">Создать пост</button></a>
    <h2 class="textTitle">Посты</h2>
    <?php if (!empty($success)): ?>
        <p class="textTitle" style="color:green"><?= $success ?></p>
    <?php endif; ?>

    <?php if (!isset($error)): ?>
        <?php foreach ($posts as $post): ?>
            <div class="conteinerPost">
                <a class="titleText" href="/post.php?id=<?= htmlspecialchars($post['id']) ?>">
                    <?= htmlspecialchars($post['title']) ?>
                </a>
                <p class="textPost"><?= date('d.m.Y', strtotime($post['date']) ?? '') ?></p>
                <p class="textPost"><?= htmlspecialchars($post['author'] ?? '') ?></p>
                <div class="elementPost">
                    <a class="textEdit" href="/post-edit.php?action=edit&id=<?= $post['id'] ?>">Редактировать</a>
                    <a class="textDelete" href="/posts.php?action=delete&id=<?= $post['id'] ?>">Удалить</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <?= htmlspecialchars($error) ?>
    <?php endif; ?>
</body>

</html>