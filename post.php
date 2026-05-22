<?php

require __DIR__ . '/vendor/autoload.php';

use function CompanyName\Blog\getPost;
use function CompanyName\Blog\redirectToError;

const STATUSES = [
    'ok' => 'Пост успешно создан',
    'edit' => 'Пост успешно изменен',
    'info' => 'Поздравляю',
];
$success = STATUSES[($_GET['success'] ?? '')] ?? '';

try {
    $id = $_GET['id'] ?? null;

    //Валидация
    if (is_null($id)) {
        throw new OutOfBoundsException('ID поста не передан');
    }

    if (!is_numeric($id)) {
        throw new OutOfBoundsException('ID поста должен быть числом');
    }

    $post = getPost($id);

} catch (OutOfBoundsException $e) {

    // http_response_code(404);
    // $error = "404 пост не найден. " . $e->getMessage();

    $errorId = 'ERR_' . date('Ymd_His') . '_' . uniqid();

    $errorDetails = [
        'message' => $e->getMessage(),
        'errorId' => $errorId,
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];
    error_log(json_encode($errorDetails, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    //Редирект
    redirectToError(404, $e->getMessage(), $errorId);
} catch (Exception $e) {
    $errorDetails = [
        'message' => $e->getMessage(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];
    error_log(json_encode($errorDetails, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
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
    <h2>Пост</h2>
    <?php if (!empty($success)): ?>
        <p class="textTitle" style="color:green"><?= $success ?></p>
    <?php endif; ?>
    <?php if (!isset($error)): ?>
        <div class="conteinerPost">
            <h3 class="titleText"><?= htmlspecialchars($post['title'] ?? '') ?></h3>
            <p class="textPost"><?= htmlspecialchars($post['content'] ?? '') ?></p>
            <span class="textPost"><?= date('d.m.Y', strtotime($post['date']) ?? '') ?></span>
            <span class="textPost"><?= htmlspecialchars($post['author'] ?? '') ?></span>
        </div>
    <?php else: ?>
        <?= htmlspecialchars($error) ?>
    <?php endif; ?>
</body>

</html>