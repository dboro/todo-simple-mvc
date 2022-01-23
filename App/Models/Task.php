<?php

namespace App\Models;

class Task
{
    public const LIMIT = 3;
    private \PDO $db;
    private string $table = 'tasks';

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAll(int $page, ?string $sortKey, ?string $sortValue): array
    {
        $sql = "SELECT * FROM {$this->table} ";

        if ($sortKey && $sortValue) {
            $sql .= " ORDER BY $sortKey $sortValue";
        }

        $limit = self::LIMIT;
        $offset = self::LIMIT * ($page - 1);
        $sql .= " LIMIT $limit OFFSET $offset;";

        $pdoStatement = $this->db->prepare($sql);
        $pdoStatement->execute();

        return $pdoStatement->fetchAll();
    }

    public function findById($id)
    {
        $pdoStatement = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE `id`=$id LIMIT 1;"
        );

        $pdoStatement->execute();

        return $pdoStatement->fetch(\PDO::FETCH_ASSOC);
    }

    public function create($data) : bool
    {
        $pdoStatement = $this->db->prepare(
            "INSERT INTO {$this->table} (`name`, `email`, `description`) VALUES (:name, :email, :description)"
        );

        return $pdoStatement->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'description' => $data['description']
        ]);
    }

    public function update($id, $data) : bool
    {
        $pdoStatement = $this->db->prepare(
            "UPDATE {$this->table} SET `description`=:description, `is_done`=:is_done WHERE id=:id;"
        );

        return $pdoStatement->execute([
            'id' => $id,
            'is_done' => $data['is_done'],
            'description' => $data['description']
        ]);
    }

    public function count() : int
    {
        $pdoStatement = $this->db->prepare(
            "SELECT COUNT(*) FROM {$this->table}"
        );

        $pdoStatement->execute();

        return $pdoStatement->fetchColumn();
    }

    public function exists($id) : bool
    {
        $pdoStatement = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE `id`=$id LIMIT 1;"
        );

        $pdoStatement->execute();

        return $pdoStatement->rowCount() > 0;
    }
}