<?php

namespace App\Modules\Posts;

use App\Core\Validator;

class PostRequest
{
    public static function validateCreate()
    {
        $data = $_POST;
        $data['img'] = $_FILES['img']['name'] ?? null;

        $data['rate'] = isset($data['rate']) ? (int)$data['rate'] : 0;
        $data['views'] = isset($data['views']) ? (int)$data['views'] : 0;

        return Validator::validate($data, [
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'category' => 'required|integer|exists:categories,id',
            'rate'        => 'sometimes|integer',
            'views'       => 'sometimes|integer',
            'user_id'     => 'required|integer|exists:users,id',
            'img'         => 'sometimes|file|image|max:2048'
        ]);
    }

    public static function validateUpdate($data): array
    {
        $rules = [
            'title'       => 'sometimes|string|max:255',
            'content'     => 'sometimes|string',
            'category' => 'sometimes|integer|exists:categories,id',
            'rate'        => 'sometimes|integer',
            'views'       => 'sometimes|integer',
            'img'         => 'sometimes|file|image|max:2048'
        ];

        $applicableRules = array_intersect_key($rules, $data);

        return Validator::validate($data, $applicableRules);
    }
}
