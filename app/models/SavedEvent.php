<?php

class SavedEvent
{
    public function __construct(private PDO $pdo) {}

    public function idsForUser(int $userId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT event_id FROM saved_events WHERE user_id = :user_id ORDER BY saved_at DESC, event_id DESC'
        );
        $statement->execute(['user_id' => $userId]);
        return array_map('intval', $statement->fetchAll(PDO::FETCH_COLUMN));
    }

    public function setSaved(int $userId, int $eventId, bool $saved): void
    {
        if ($saved) {
            $statement = $this->pdo->prepare(
                'INSERT INTO saved_events (user_id, event_id) VALUES (:user_id, :event_id)
                 ON DUPLICATE KEY UPDATE event_id = VALUES(event_id)'
            );
            $statement->execute(['user_id' => $userId, 'event_id' => $eventId]);
            return;
        }

        $statement = $this->pdo->prepare(
            'DELETE FROM saved_events WHERE user_id = :user_id AND event_id = :event_id'
        );
        $statement->execute(['user_id' => $userId, 'event_id' => $eventId]);
    }

    public function mergeForUser(int $userId, array $eventIds): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO saved_events (user_id, event_id)
             SELECT :user_id, e.id FROM events e WHERE e.id = :event_id
             ON DUPLICATE KEY UPDATE event_id = VALUES(event_id)'
        );

        $this->pdo->beginTransaction();
        try {
            foreach ($eventIds as $eventId) {
                $statement->execute([
                    'user_id' => $userId,
                    'event_id' => $eventId,
                ]);
            }
            $this->pdo->commit();
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }
}
