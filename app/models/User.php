<?php

class User
{
    public function __construct(private PDO $pdo) {}

    public function findByEmail(string $email): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();
        return $user ?: null;
    }

    public function emailExists(string $email): bool
    {
        $statement = $this->pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $statement->execute(['email' => $email]);
        return (bool) $statement->fetchColumn();
    }

    public function create(array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO users (name, email, phone, password_hash, role, region_id)
            VALUES (:name, :email, :phone, :password_hash, \'attendee\', :region_id)'
        );
        $statement->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password_hash' => $data['password_hash'],
            'region_id' => $data['region_id'] ?: null,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function findById(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE id=:id LIMIT 1');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();
        return $row ?: null;
    }

    public function updateProfile(int $id, array $data): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE users SET name=:name, phone=:phone, region_id=:region_id WHERE id=:id'
        );
        $statement->execute([
            'name' => $data['name'], 'phone' => $data['phone'],
            'region_id' => $data['region_id'] ?: null, 'id' => $id,
        ]);
    }

    public function changePassword(int $id, string $currentPassword, string $newPassword): bool
    {
        $user = $this->findById($id);
        if (!$user || !password_verify($currentPassword, $user['password_hash'])) return false;
        $statement = $this->pdo->prepare('UPDATE users SET password_hash=:password_hash WHERE id=:id');
        $statement->execute([
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT), 'id' => $id,
        ]);
        return true;
    }
}