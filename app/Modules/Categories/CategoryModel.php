<?php

namespace App\Modules\Categories;

use App\Core\Database;
use PDO;

class CategoryModel
{
    protected static string $table = "categories";

    public static function getAll(): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT * FROM " . self::$table . " ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(int $id): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM " . self::$table . " WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("INSERT INTO " . self::$table . " (category) VALUES (?) RETURNING id");
        $stmt->execute([$data['category']]);
        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['id'];
    }

    public static function update(int $id, array $data): bool
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("UPDATE " . self::$table . " SET category = ? WHERE id = ?");
        return $stmt->execute([$data['category'], $id]);
    }

    public static function delete(int $id): bool
    {
        $pdo = Database::connect();

        // Verificar se há posts associados
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            return false; // Não apagar se houver posts
        }

        $stmt = $pdo->prepare("DELETE FROM " . self::$table . " WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
