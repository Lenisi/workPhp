<?php
require __DIR__ . '/vendor/autoload.php';

use function CompanyName\Blog\getCategories;
use function CompanyName\Blog\getPost;
use function CompanyName\Blog\editPosts;
use function CompanyName\Blog\redirectToError;


$categories = getCategories();
$category_id = null;
$post = [];


try {
    // Редактированиен
    if (isset($_GET['action']) && $_GET['action'] === 'edit') {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            throw new OutOfBoundsException('ID поста не передан');
        }

        $id = (int) $_GET['id'];
        $post = getPost($id);
    }
    // Сохранение
    if (isset($_GET['action']) && $_GET['action'] === 'save') {
        $id = (int) ($_POST['id'] ?? 0);
        $title = htmlspecialchars($_POST['title'] ?? '');
        $content = htmlspecialchars($_POST['content'] ?? '');
        $date = htmlspecialchars($_POST['date'] ?? '');
        $author = htmlspecialchars($_POST['author'] ?? '');
        $category_id = (int) ($_POST['category_id'] ?? null);

        $errors = [];

        // Валидация 
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
            editPosts($id, [
                'category_id' => $category_id,
                'title' => $title,
                'content' => $content,
                'date' => $date,
                'author' => $author
            ]);

            header("Location: /post.php?id=$id&success=edit");
            die();
        }
    }
} catch (OutOfBoundsException $e) {
    $errorId = 'ERR_' . date('Ymd_His') . '_' . uniqid();

    error_log(json_encode([
        'message' => $e->getMessage(),
        'errorId' => $errorId,
    ], JSON_UNESCAPED_UNICODE));

    redirectToError(404, $e->getMessage(), $errorId);
} catch (Exception $e) {
    $errorId = 'ERR_' . date('Ymd_His') . '_' . uniqid();

    error_log(json_encode(['message' => $e->getMessage(), 'errorId' => $errorId,], JSON_UNESCAPED_UNICODE));

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
        <h2 class="textTitle">Править пост пост</h2>
        <form action="/post-edit.php?action=save" method="post" enctype="application/x-www-form-urlencoded">
            <input type="text" name="id" readonly hidden value="<?= $post['id'] ?? '' ?>">
            <p class="text">Категория:</p>
            <select name="category_id">
                <?php foreach ($categories as $category): ?>
                    <option <?= ($category['id'] == ($post['category_id'] ?? $category_id)) ? 'selected' : '' ?>
                        value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                <?php endforeach; ?>
            </select>

            <p class="text">Заголовок поста:</p>
            <input type="text" name="title" value="<?= $post['title'] ?? $title ?? '' ?>">
            <?php if (!empty($errors['title'])): ?>
                <p style="color:red"><?= $errors['title'] ?></p>
            <?php endif; ?>
            <p class="text">Текст поста:</p>
            <textarea name="content"><?= $post['content'] ?? $content ?? '' ?></textarea>
            <?php if (!empty($errors['content'])): ?>
                <p style="color:red"><?= $errors['content'] ?></p>
            <?php endif; ?>
            <p class="text">Дата:</p>
            <?php $dateValue = $post['date'] ?? ''; ?>
            <input type="date" name="date" value="<?= htmlspecialchars($dateValue) ?>">
            <?php if (!empty($errors['date'])): ?>
                <p style="color:red"><?= $errors['date'] ?></p>
            <?php endif; ?>
            <p class="text">Автор:</p>
            <input type="text" name="author" value="<?= $post['author'] ?? '' ?>">
            <?php if (!empty($errors['author'])): ?>
                <p style="color:red"><?= $errors['author'] ?></p>
            <?php endif; ?>
            <input class="editbtn" type="submit" value="Изменить">


        </form>
    </div>
</body>

</html>