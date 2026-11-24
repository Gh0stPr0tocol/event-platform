<?php

class Region
{
    public function __construct(private PDO $pdo) {}

    public function all(): array
    {
        $statement = $this->pdo->prepare('SELECT id, name FROM regions ORDER BY name');
        $statement->execute();
        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT id, name FROM regions WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();
        return $row ?: null;
    }
}