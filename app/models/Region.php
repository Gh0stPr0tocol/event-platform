<?php

class Region
{
    public function __construct(private PDO $pdo) {}

    public function all(): array
    {
        return $this->pdo->query('SELECT id, name FROM regions ORDER BY name')->fetchAll();
    }

    public function list(int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $count = (int) $this->pdo->query('SELECT COUNT(*) FROM regions')->fetchColumn();
        $statement = $this->pdo->prepare(
            'SELECT r.*, (SELECT COUNT(*) FROM venues v WHERE v.region_id = r.id) AS venue_count
             FROM regions r ORDER BY r.name LIMIT :limit OFFSET :offset'
        );
        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        return ['items' => $statement->fetchAll(), 'total' => $count];
    }

    public function find(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT id, name FROM regions WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();
        return $row ?: null;
    }
}