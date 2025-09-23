<?php

namespace App\Modules\Contacts;

class ContactService
{
    private $model;

    public function __construct()
    {
        $this->model = new ContactModel();
    }

    public function create(array $data): int
    {
        return $this->model->create($data);
    }

    public function getAll(): array
    {
        return $this->model->getAll();
    }

    public function getById(int $id): ?array
    {
        return $this->model->getById($id);
    }

    public function delete(int $id): bool
    {
        return $this->model->delete($id);
    }

    public function markAsViewed(int $id): bool
    {
        return $this->model->markAsViewed($id);
    }
}
