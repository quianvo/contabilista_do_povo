<?php

namespace App\Modules\Categories;

class CategoryService
{
    public function getAll(): array
    {
        return CategoryModel::getAll();
    }

    public function getById(int $id): array
    {
        $category = CategoryModel::find($id);
        if (!$category) {
            throw new \RuntimeException("Category not found", 404);
        }
        return $category;
    }

    public function create(array $data): int
    {
        return CategoryModel::create($data);
    }

    public function update(int $id, array $data): array
    {
        if (!CategoryModel::update($id, $data)) {
            throw new \RuntimeException("Failed to update category", 400);
        }
        return $this->getById($id);
    }

    public function delete(int $id): bool
    {
        $deleted = CategoryModel::delete($id);
        if (!$deleted) {
            throw new \RuntimeException("Cannot delete category with posts associated", 400);
        }
        return true;
    }
}
