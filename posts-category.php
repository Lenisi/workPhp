<?php

require __DIR__ . '/vendor/autoload.php';

use function CompanyName\Blog\getCategoryBySlug;
use function CompanyName\Blog\getPostsCategoriesBySlug;
use function CompanyName\Blog\redirectToError;

try {

    $slug = $_GET['category'] ?? null;

    if (is_null($slug) || $slug === '') {
        throw new OutOfBoundsException('Slug категории не передан');
    }

    $category = getCategoryBySlug($slug);

    if (!$category) {
        throw new OutOfBoundsException('Категория не найдена');
    }

    $posts = getPostsCategoriesBySlug($slug);

    if (empty($posts)) {
        $posts = [];
    }
} catch (OutOfBoundsException $e) {

    $errorDetails = [
        'message' => $e->getMessage(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];
    error_log(json_encode($errorDetails, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    //Редирект
    redirectToError(404, $e->getMessage());

} catch (Exception $e) {
    $errorDetails = [
        'message' => $e->getMessage(),
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
    <h2 class="textTitle">Посты категории <?= htmlspecialchars($category['name'] ?? '') ?></h2>
    <?php if (!isset($error)): ?>
        <?php foreach ($posts as $post): ?>
            <div class="conteinerCategoriesDetal">
                <h3 class="textPost">
                    <a class="titleText" href="/post.php?id=<?= htmlspecialchars($post['id']) ?>">
                        <?= htmlspecialchars($post['title']) ?>
                    </a>
                </h3>
                <p class="textPost"><?= date('d.m.Y', strtotime($post['date']) ?? '') ?></p>
                <p class="textPost"><?= htmlspecialchars($post['author'] ?? '') ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <?= htmlspecialchars($error) ?>
    <?php endif; ?>

</body>

</html>