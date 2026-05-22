<?php
$errorConfig = [
    404 => [
        'title' => 'Страница не найдена',
        'message' => 'Запрашиваемая страница не существует или была перемещена.',
        'suggestions' => [
            'Проверьте правильность URL адреса',
            'Вернитесь на главную страницу',
            'Воспользуйтесь поиском по сайту'
        ]
    ],
    500 => [
        'title' => 'Внутренняя ошибка сервера',
        'message' => 'На сервере произошла техническая ошибка.',
        'suggestions' => [
            'Попробуйте обновить страницу через несколько минут',
            'Очистите кэш браузера',
            'Сообщите об ошибке администратору',
            'Попробуйте зайти позже'
        ]
    ]
];

$errorCode = isset($_GET['code']) ? (int) $_GET['code'] : 404;
$errorMessage = isset($_GET['message']) ? urldecode($_GET['message']) : null;
$errorId = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;

if (!array_key_exists($errorCode, $errorConfig)) {
    $errorCode = 404;
}

$config = $errorConfig[$errorCode] ?? $errorConfig[404];
if ($errorMessage) {
    $config['message'] = htmlspecialchars($errorMessage);
}

http_response_code($errorCode);

header('X-Robots-Tag: noindex, nofollow');
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Ошибка <?= $errorCode ?> - <?= htmlspecialchars($config['title']) ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>

<body>

    <div class="error-container">
        <div class="error-code"><?= $errorCode ?></div>
        <h1><?= htmlspecialchars($config['title']) ?></h1>
        <p class="error-message"><?= nl2br(htmlspecialchars($config['message'])) ?></p>

        <?php if (isset($errorId)): ?>
        <div class="error-id">
            <strong>Код ошибки:</strong> <?= htmlspecialchars($errorId) ?>
            <br>
            <small>Пожалуйста, сообщите этот код в службу поддержки</small>
        </div>
        <?php endif; ?>

        <div class="suggestions">
            <h3>Что можно сделать:</h3>
            <ul>
                <?php foreach ($config['suggestions'] as $suggestion): ?>
                <li><?= $suggestion ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="buttons">
            <a href="/" class="btn">На главную</a>
            <button onclick="history.back()" class="btn btn-secondary">Назад</button>
        </div>
</body>

</html>