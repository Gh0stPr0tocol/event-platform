<?php

class Category
{
    public function __construct(private PDO $pdo) {}

    public function all(): array
    {
        $statement = $this->pdo->prepare('SELECT id, name FROM categories ORDER BY name');
        $statement->execute();
        return $statement->fetchAll();
    }

    public function list(int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $countStatement = $this->pdo->prepare('SELECT COUNT(*) FROM categories');
        $countStatement->execute();
        $count = (int) $countStatement->fetchColumn();
        $statement = $this->pdo->prepare(
            'SELECT c.*,
                    (SELECT COUNT(*) FROM events e WHERE e.category_id = c.id) AS event_count
             FROM categories c ORDER BY c.name LIMIT :limit OFFSET :offset'
        );
        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        return ['items' => $statement->fetchAll(), 'total' => $count];
    }

    public function create(string $name): void
    {
        $statement = $this->pdo->prepare('INSERT INTO categories (name) VALUES (:name)');
        $statement->execute(['name' => trim($name)]);
    }

    public function update(int $id, string $name): void
    {
        $statement = $this->pdo->prepare('UPDATE categories SET name=:name WHERE id=:id');
        $statement->execute(['name' => trim($name), 'id' => $id]);
    }

    public function delete(int $id): void
    {
        try {
            $statement = $this->pdo->prepare('DELETE FROM categories WHERE id=:id');
            $statement->execute(['id' => $id]);
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                throw new DomainException('This category is in use and cannot be deleted.');
            }
            throw $exception;
        }
    }

    public function find(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM categories WHERE id=:id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();
        return $row ?: null;
    }

    public function nameExists(string $name, ?int $exceptId = null): bool
    {
        $sql = 'SELECT id FROM categories WHERE LOWER(name)=LOWER(:name)';
        $params = ['name' => trim($name)];
        if ($exceptId) { $sql .= ' AND id<>:id'; $params['id'] = $exceptId; }
        $sql .= ' LIMIT 1';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return (bool) $statement->fetchColumn();
    }
}