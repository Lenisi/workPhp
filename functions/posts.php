<?php

namespace CompanyName\Blog;

use OutOfRangeException;

// Сохранение постов в json
function savePost(array $post): int
{
    $posts = getPosts();
    $posts[] = $post;
    $lastKey = array_key_last($posts);
    $posts[$lastKey]['id'] = $lastKey;

    file_put_contents(__DIR__ . '/../data/posts.json', json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    return $lastKey;
}

// Функция на удаление постов
function deletePost(int $id): void
{
    $posts = getPosts();

    if (!isset($posts[$id])) {
        throw new OutOfRangeException('Пост не найден');
    }
    unset($posts[$id]);

    file_put_contents(__DIR__ . '/../data/posts.json', json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

function editPosts(int $id, array $post): void
{
    $posts = getPosts();
    if (!isset($posts[$id])) {
        throw new OutOfRangeException('Пост не найден');
    }
    $posts[$id] = array_merge($posts[$id], $post);
    $posts[$id]['id'] = $id;

    file_put_contents(__DIR__ . '/../data/posts.json', json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

function getPost(int $id): array
{
    $posts = getPosts();

    if (!isset($posts[$id])) {
        throw new \OutOfBoundsException("Пост не найден");
    }

    return $posts[$id];
}

function getPosts(): array
{
    $postsData = readFileData('posts.json');
    return decodeData($postsData);
}


function getPostsCategoriesBySlug(string $slug): array
{
    $category = getCategoryBySlug($slug);

    return getPostsCategoriesById($category['id']);
}


function getPostsCategoriesById(int $id): array
{
    $posts = getPosts();

    $filteredPosts = array_filter($posts, function ($post) use ($id) {
        return isset($post['category_id']) && $post['category_id'] === $id;
    });

    return array_values($filteredPosts);
}
