<?php

namespace App\Modules\Categories;

use App\Core\Validator;

class CategoryRequest
{
    public static function validateCreate(array $data): array
    {
        return Validator::validate($data, [
            'category' => 'required|string|max:100|unique:categories,category'
        ]);
    }

    public static function validateUpdate(array $data): array
    {
        return Validator::validate($data, [
            'category' => 'sometimes|string|max:100'
        ]);
    }
}
