<?php

namespace App\Modules\Contacts;

class ContactRequest
{
    public static function validateCreate(array $data): array
    {
        if (empty($data['name'])) {
            throw new \Exception("O campo nome é obrigatório");
        }
        if (empty($data['email'])) {
            throw new \Exception("O campo email é obrigatório");
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Email inválido");
        }
        if (empty($data['topic'])) {
            throw new \Exception("O campo tópico é obrigatório");
        }
        if (empty($data['content'])) {
            throw new \Exception("O campo conteúdo é obrigatório");
        }

        return [
            'name' => htmlspecialchars(trim($data['name'])),
            'email' => strtolower(trim($data['email'])),
            'topic' => htmlspecialchars(trim($data['topic'])),
            'telephone' => trim($data['telephone']),
            'content' => trim($data['content']),
        ];
    }
}
