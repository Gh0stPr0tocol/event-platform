<?php

class Venue
{
    public function __construct(private PDO $pdo) {}

    public function all(): array
    {
        return $this->pdo->query(
            'SELECT v.*, r.name AS region_name FROM venues v
             JOIN regions r ON r.id=v.region_id ORDER BY r.name, v.name'
        )->fetchAll();
    }

    public function list(int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $count = (int) $this->pdo->query('SELECT COUNT(*) FROM venues')->fetchColumn();
        $statement = $this->pdo->prepare(
            'SELECT v.*, r.name AS region_name,
                    (SELECT COUNT(*) FROM events e WHERE e.venue_id = v.id) AS event_count
             FROM venues v JOIN regions r ON r.id = v.region_id
             ORDER BY r.name, v.name LIMIT :limit OFFSET :offset'
        );
        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        return ['items' => $statement->fetchAll(), 'total' => $count];
    }

    public function create(array $data): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO venues (name,address,region_id) VALUES (:name,:address,:region_id)'
        );
        $statement->execute($data);
    }

    public function update(int $id, array $data): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE venues SET name=:name,address=:address,region_id=:region_id WHERE id=:id'
        );
        $statement->execute(['name'=>$data['name'],'address'=>$data['address'],'region_id'=>$data['region_id'],'id'=>$id]);
    }

    public function delete(int $id): void
    {
        try {
            $statement = $this->pdo->prepare('DELETE FROM venues WHERE id=:id');
            $statement->execute(['id' => $id]);
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                throw new DomainException('This venue is in use and cannot be deleted.');
            }
            throw $exception;
        }
    }

    public function find(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM venues WHERE id=:id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();
        return $row ?: null;
    }
}