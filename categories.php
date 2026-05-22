<?php

require __DIR__ . '/vendor/autoload.php';

use function CompanyName\Blog\getCategories;
use function CompanyName\Blog\redirectToError;

try {

    $categories = getCategories();

} catch (Exception $e) {

    $errorDetails = [
        'message' => $e->getMessage(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];
    error_log(json_encode($errorDetails, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    //Редирект
    redirectToError(500);

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
    <h2 class="title">Посты</h2>
    <div class="conteinerCategories">
        <?php if (isset($categories)): ?>
            <?php foreach ($categories as $category): ?>
                <a class="textCategories" href="/posts-category.php?category=<?= htmlspecialchars($category['slug']) ?>">
                    <?= htmlspecialchars($category['name']) ?><br>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <?= htmlspecialchars($error) ?>
        <?php endif; ?>
    </div>
</body>

</html>