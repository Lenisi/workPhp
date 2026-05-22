<?php
require __DIR__ . '/vendor/autoload.php';

// use function CompanyName\Blog\getPosts;
use function CompanyName\Blog\getCategories;
use function CompanyName\Blog\redirectToError;
use function CompanyName\Blog\savePost;


try {
    $categories = getCategories();
    $category_id = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = htmlspecialchars($_POST['title'] ?? '');
        $content = htmlspecialchars($_POST['content'] ?? '');
        $date = htmlspecialchars($_POST['date'] ?? '');
        $author = htmlspecialchars($_POST['author'] ?? '');
        $category_id = (int) ($_POST['category_id'] ?? null);

        $errors = [];

        if (empty($title)) {
            $errors['title'] = 'Заполните поле Заголовка';
        }

        if (empty($content)) {
            $errors['content'] = 'Заполните поле Контента';
        }

        if (empty($date)) {
            $errors['date'] = 'Заполните поле Дата';
        }

        if (empty($author)) {
            $errors['author'] = 'Заполните поле Автор';
        }

        if (empty($errors)) {
            $id = savePost([
                'category_id' => $category_id,
                'title' => $title,
                'content' => $content,
                'date' => $date,
                'author' => $author
            ]);

            header("Location: /post.php?id=$id&success=ok");
            die();
        }
    }
} catch (Exception $e) {
    $errorId = 'ERR_' . date('Ymd_His') . '_' . uniqid();

    error_log(json_encode([
        'message' => $e->getMessage(),
        'errorId' => $errorId,
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    redirectToError(500, $e->getMessage(), $errorId);
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="/css/style.css">
</head>

<body>
    <?php include __DIR__ . '/components/menu.php' ?>
    <div class="conteiner">
        <h2 class="textTitle">Cоздать пост</h2>
        <form action="" method="post" enctype="application/x-www-form-urlencoded">
            <p class="text">Категория:</p>
            <select name="category_id">
                <?php foreach ($categories as $category): ?>
                    <option <?= ($category['id'] === $category_id) ? 'selected' : '' ?> value="<?= $category['id'] ?>">
                        <?= $category['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <p class="text">Заголовок поста:</p>
            <input type="text" name="title" value="<?= $title ?? '' ?>">
            <?php if (!empty($errors['title'])): ?>
                <p style="color:red"><?= $errors['title'] ?></p>
            <?php endif; ?>
            <p class="text">Текст поста:</p>
            <textarea name="content"><?= $content ?? '' ?></textarea>
            <?php if (!empty($errors['content'])): ?>
                <p style="color:red"><?= $errors['content'] ?></p>
            <?php endif; ?>
            <p class="text">Дата:</p>
            <input type="date" name="date" value="<?= $date ?? '' ?>">
            <?php if (!empty($errors['date'])): ?>
                <p style=" color:red"><?= $errors['date'] ?></p>
            <?php endif; ?>
            <p class="text">Автор:</p>
            <input type="text" name="author" value="<?= $author ?? '' ?>">
            <?php if (!empty($errors['author'])): ?>
                <p style="color:red"><?= $errors['author'] ?></p>
            <?php endif; ?>
            <input class="createBtn" type="submit" value="Создать">

            <!--
    <input type="checkbox" name="tags[]" value="Политика">
    <input type="checkbox" name="tags[]" value="Жесть">
    <input type="checkbox" name="tags[]" value="Еда">
    -->
        </form>
    </div>
</body>

</html>