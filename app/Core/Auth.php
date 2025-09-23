<?php

namespace App\Core;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth
{
    private static ?string $secretKey = null;
    private static string $algorithm = 'HS256';
    private static $user = null;

    private static function init(): void
    {
        self::$secretKey = "secretKey";
        if (!self::$secretKey) {
            self::$secretKey = "secretKey";
        }
    }

    public static function check(): bool
    {
        self::init();
        $token = self::getBearerToken();

        if (!$token) {
            return false;
        }

        try {
            $decoded = JWT::decode($token, new Key(self::$secretKey, self::$algorithm));
            self::$user = $decoded;
            return !empty($decoded->id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function user()
    {
        return self::$user;
    }

    public static function generateToken(array $userData): string
    {
        self::init();
        $payload = [
            'id'    => $userData['id'],
            'email' => $userData['email'],
            'iat'   => time(),
            'exp'   => time() + (60 * 60)
        ];

        return JWT::encode($payload, self::$secretKey, self::$algorithm);
    }

    private static function getBearerToken(): ?string
    {
        $headers = getallheaders();

        if (isset($headers['Authorization'])) {
            if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    public static function id(): ?int
    {
        return self::$user->id ?? null;
    }
}
