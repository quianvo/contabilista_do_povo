<?php

namespace App\Modules\Auth;

use App\Core\Database;
use App\Services\JWTService;

class AuthController
{
    public function register()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $pdo = Database::connect();

        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        $stmt->execute([$data['name'], $data['email'], $hashedPassword]);

        echo json_encode(["message" => "User registered successfully"]);
    }

    public function login()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        $user = $stmt->fetch();
        if ($user && password_verify($data['password'], $user['password'])) {
            $token = \App\Core\Auth::generateToken([
                "id" => $user['id'],
                "email" => $user['email']
            ]);
            echo json_encode(["token" => $token]);
        } else {
            http_response_code(401);
            echo json_encode(["message" => "Invalid credentials"]);
        }
    }
}
