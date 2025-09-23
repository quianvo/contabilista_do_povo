<?php

namespace App\Core;

class Validator
{
    public static function validate(array $data, array $rules): array
    {
        $validated = [];
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $individualRules = explode('|', $ruleString);  // <- nome diferente

            foreach ($individualRules as $rule) {
                $value = $data[$field] ?? null;

                if ($rule === 'required' && empty($value)) {
                    $errors[$field][] = "O campo $field é obrigatório";
                }

                //Devo colocar aqui as outras validações Não equecer"
            }

            if (!isset($errors[$field])) {
                $validated[$field] = $data[$field];
            }
        }


        if (!empty($errors)) {
            Response::json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors
            ], 422);
            exit;
        }

        return $validated;
    }

}
