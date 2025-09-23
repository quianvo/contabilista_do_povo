<?php

namespace App\Modules\Categories;

use App\Core\Request;
use App\Core\Response;
use App\Middleware\AuthMiddleware;

class CategoryController
{
    protected CategoryService $service;

    public function __construct()
    {
        (new AuthMiddleware())->handle(Request::capture(), fn($r) => null);
        $this->service = new CategoryService();
    }

    public function index()
    {
        $categories = $this->service->getAll();
        Response::json($categories);
    }

    public function show(int $id)
    {
        $category = $this->service->getById($id);
        Response::json($category);
    }

public function create()
{
    $request = Request::capture();
    $data = $request->all();

    $validated = CategoryRequest::validateCreate($data);
    $id = $this->service->create($validated);

    Response::json(['message' => 'Category created', 'id' => $id], 201);
}

public function update(int $id)
{
    $request = Request::capture();
    $data = $request->all();

    $validated = CategoryRequest::validateUpdate($data);
    $category = $this->service->update($id, $validated);

    Response::json(['message' => 'Category updated', 'data' => $category]);
}


    public function delete(int $id)
    {
        $this->service->delete($id);
        Response::json(['message' => 'Category deleted']);
    }
}
