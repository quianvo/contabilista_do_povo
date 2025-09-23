<?php

namespace App\Modules\Contacts;

use App\Core\Database;
use PDO;

class ContactModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO contacts (name, email, topic, content, telephone)
            VALUES (:name, :email, :topic, :content, :telephone)
        ");
        $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':topic' => $data['topic'],
            ':content' => $data['content'],
            ':telephone' => $data['telephone'],
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM contacts ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM contacts WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $contact = $stmt->fetch(PDO::FETCH_ASSOC);

        return $contact ?: null;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM contacts WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function markAsViewed(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE contacts SET viewed = TRUE WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
