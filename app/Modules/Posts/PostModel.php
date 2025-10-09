<?php

namespace App\Modules\Posts;

use App\Core\Database;
use PDO;

class PostModel
{
    protected static string $table = 'posts';

    public static function getPosts(?string $category = null, int $page = 1, int $perPage = 3): array
    {
        try {
            $pdo = Database::connect();
            $offset = ($page - 1) * $perPage;

            if ($category) {
                // Normaliza categoria para minúsculas
                $category = strtolower($category);

                // Conta total de posts dessa categoria (case-insensitive)
                $countStmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM " . self::$table . " p
                JOIN categories c ON LOWER(p.category) = LOWER(c.category)
                WHERE LOWER(c.category) = :category
            ");
                $countStmt->bindValue(':category', $category, PDO::PARAM_STR);
                $countStmt->execute();
                $total = (int) $countStmt->fetchColumn();

                // Seleciona posts da categoria
                $stmt = $pdo->prepare("
                SELECT p.*, c.category AS category_name
                FROM " . self::$table . " p
                JOIN categories c ON LOWER(p.category) = LOWER(c.category)
                WHERE LOWER(c.category) = :category
                ORDER BY p.created_at DESC
                LIMIT :perPage OFFSET :offset
            ");
                $stmt->bindValue(':category', $category, PDO::PARAM_STR);
            } else {
                $countStmt = $pdo->query("SELECT COUNT(*) FROM " . self::$table);
                $total = (int) $countStmt->fetchColumn();

                $stmt = $pdo->prepare("
                SELECT p.*, c.category AS category_name
                FROM " . self::$table . " p
                JOIN categories c ON LOWER(p.category) = LOWER(c.category)
                ORDER BY p.created_at DESC
                LIMIT :perPage OFFSET :offset
            ");
            }

            $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                "page" => $page,
                "per_page" => $perPage,
                "total" => $total,
                "total_pages" => ceil($total / $perPage),
                "data" => $posts
            ];
        } catch (\PDOException $e) {
            error_log("Error in getPosts: " . $e->getMessage());
            return [
                "page" => $page,
                "per_page" => $perPage,
                "total" => 0,
                "total_pages" => 0,
                "data" => []
            ];
        }
    }

    public static function getAllPosts(): array
    {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->query("
            SELECT p.*, c.category AS category_name
            FROM " . self::$table . " p
            JOIN categories c ON LOWER(p.category) = LOWER(c.category)
            ORDER BY p.created_at DESC
        ");
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $posts;
        } catch (\PDOException $e) {
            error_log("Error in getAllPosts: " . $e->getMessage());
            return [];
        }
    }


public static function find(int $id): ?array
{
    try {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("
            SELECT p.*, c.category AS category_name
            FROM " . self::$table . " p
            JOIN categories c ON LOWER(p.category) = LOWER(c.category)
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (\PDOException $e) {
        error_log("Error in find: " . $e->getMessage());
        return null;
    }
}


    public static function create(array $data): int
    {
        try {
            $pdo = Database::connect();

            // normaliza o campo category
            if (isset($data['category'])) {
                $data['category'] = strtolower(trim($data['category']));
            }

            $columns = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            $sql = "INSERT INTO " . self::$table . " ($columns) VALUES ($placeholders) RETURNING id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute(array_values($data));

            return $stmt->fetch(\PDO::FETCH_ASSOC)['id'];
        } catch (\PDOException $e) {
            error_log("Error in create: " . $e->getMessage());
            return 0;
        }
    }


    public static function update(int $id, array $data): bool
    {
        try {
            $pdo = Database::connect();
            $data = array_filter($data, fn($value) => $value !== null && $value !== '');

            if (empty($data)) return false;

            $setClause = implode(' = ?, ', array_keys($data)) . ' = ?';
            $sql = "UPDATE " . self::$table . " SET $setClause WHERE id = ?";
            $stmt = $pdo->prepare($sql);

            $values = array_values($data);
            $values[] = $id;

            return $stmt->execute($values);
        } catch (\PDOException $e) {
            error_log("Error in update: " . $e->getMessage());
            return false;
        }
    }

    public static function delete(int $id): bool
    {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare("DELETE FROM " . self::$table . " WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            error_log("Error in delete: " . $e->getMessage());
            return false;
        }
    }

    public static function getAllCategories(): array
    {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->query("SELECT id, category FROM categories ORDER BY category");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in getAllCategories: " . $e->getMessage());
            return [];
        }
    }
}
