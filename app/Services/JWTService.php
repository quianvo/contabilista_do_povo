<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTService {
    public static function generate(array $payload): string {
        $key = $_ENV['JWT_SECRET'];
        $payload['iat'] = time();
        $payload['exp'] = time() + 3600; // 1h
        return JWT::encode($payload, $key, 'HS256');
    }

    public static function verify($token): object {
        $key = $_ENV['JWT_SECRET'];
        return JWT::decode($token, new Key($key, 'HS256'));
    }
}
