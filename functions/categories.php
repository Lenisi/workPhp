<?php
namespace CompanyName\Blog;


function getCategoryBySlug(string $slug): array
{
    $categories = getCategories();
    $filtered = array_filter($categories, fn($cat) => $cat['slug'] === $slug);

    if (empty($filtered)) {
        throw new \OutOfBoundsException("Категория с slug '{$slug}' не найдена");
    }

    return array_values($filtered)[0];
}

function getCategoryById(int $id): array
{
    $category = getCategories();

    if (!isset($category[$id])) {
        throw new \OutOfBoundsException("Категория не найдена");
    }

    return $category[$id];
}

function getCategories()
{
    $categoriesData = readFileData('categories.json');
    return decodeData($categoriesData);
}

