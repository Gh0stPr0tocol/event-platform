<?php

class Registration
{
    public function __construct(private PDO $pdo) {}

    public function create(int $userId, int $eventId): array
    {
        $this->pdo->beginTransaction();
        try {
            $event = $this->pdo->prepare(
                'SELECT id, capacity, event_date FROM events WHERE id = :id FOR UPDATE'
            );
            $event->execute(['id' => $eventId]);
            $eventRow = $event->fetch();
            if (!$eventRow) throw new DomainException('Event not found.');
            if ($eventRow['event_date'] < date('Y-m-d')) throw new DomainException('This event is already past.');

            $existing = $this->pdo->prepare(
                'SELECT id, status FROM registrations WHERE user_id = :uid AND event_id = :eid FOR UPDATE'
            );
            $existing->execute(['uid' => $userId, 'eid' => $eventId]);
            $registration = $existing->fetch();
            if ($registration && $registration['status'] !== STATUS_CANCELLED) {
                throw new DomainException('You are already registered for this event.');
            }

            $count = $this->pdo->prepare(
                "SELECT COUNT(*) FROM registrations WHERE event_id = :eid AND status = 'confirmed'"
            );
            $count->execute(['eid' => $eventId]);
            $confirmed = (int) $count->fetchColumn();
            $status = $confirmed < (int) $eventRow['capacity'] ? STATUS_CONFIRMED : STATUS_WAITLISTED;

            if ($registration) {
                $update = $this->pdo->prepare(
                    'UPDATE registrations SET status = :status, created_at = CURRENT_TIMESTAMP
                     WHERE id = :id'
                );
                $update->execute(['status' => $status, 'id' => $registration['id']]);
                $id = (int) $registration['id'];
            } else {
                $insert = $this->pdo->prepare(
                    'INSERT INTO registrations (user_id, event_id, status)
                     VALUES (:uid, :eid, :status)'
                );
                $insert->execute(['uid' => $userId, 'eid' => $eventId, 'status' => $status]);
                $id = (int) $this->pdo->lastInsertId();
            }
            $this->pdo->commit();
            return ['id' => $id, 'status' => $status];
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function findByUser(int $userId, int $page = 1, int $perPage = 10, string $tab = 'upcoming'): array
    {
        $offset = ($page - 1) * $perPage;
        $condition = $tab === 'past'
            ? "(e.event_date < CURDATE() OR r.status IN ('attended','cancelled'))"
            : "e.event_date >= CURDATE() AND r.status IN ('confirmed','waitlisted')";
        $sql = "SELECT r.*, e.title, e.event_date, e.start_time, e.capacity,
                       v.name AS venue_name, v.address, reg.name AS region_name
                FROM registrations r
                JOIN events e ON e.id = r.event_id
                JOIN venues v ON v.id = e.venue_id
                JOIN regions reg ON reg.id = v.region_id
                WHERE r.user_id = :uid AND $condition
                ORDER BY e.event_date ASC, e.start_time ASC
                LIMIT :limit OFFSET :offset";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':uid', $userId, PDO::PARAM_INT);
        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        $rows = $statement->fetchAll();

        $countSql = "SELECT COUNT(*) FROM registrations r JOIN events e ON e.id = r.event_id
                     WHERE r.user_id = :uid AND $condition";
        $count = $this->pdo->prepare($countSql);
        $count->bindValue(':uid', $userId, PDO::PARAM_INT);
        $count->execute();
        return ['items' => $rows, 'total' => (int) $count->fetchColumn()];
    }

    public function cancel(int $registrationId, int $userId): void
    {
        $this->pdo->beginTransaction();
        try {
            $reference = $this->pdo->prepare(
                'SELECT event_id FROM registrations WHERE id = :id AND user_id = :uid'
            );
            $reference->execute(['id' => $registrationId, 'uid' => $userId]);
            $eventId = $reference->fetchColumn();
            if ($eventId === false) throw new DomainException('Registration not found.');

            $eventLock = $this->pdo->prepare('SELECT id FROM events WHERE id = :id FOR UPDATE');
            $eventLock->execute(['id' => (int) $eventId]);
            if ($eventLock->fetchColumn() === false) throw new DomainException('Event not found.');

            $statement = $this->pdo->prepare(
                'SELECT r.id, r.status, r.event_id, e.event_date
                 FROM registrations r JOIN events e ON e.id = r.event_id
                 WHERE r.id = :id AND r.user_id = :uid FOR UPDATE'
            );
            $statement->execute(['id' => $registrationId, 'uid' => $userId]);
            $row = $statement->fetch();
            if (!$row) throw new DomainException('Registration not found.');
            if ($row['event_date'] <= date('Y-m-d')) throw new DomainException('Only future events can be cancelled.');
            if (!in_array($row['status'], [STATUS_CONFIRMED, STATUS_WAITLISTED], true)) {
                throw new DomainException('This registration cannot be cancelled.');
            }

            $update = $this->pdo->prepare('UPDATE registrations SET status = :status WHERE id = :id');
            $update->execute(['status' => STATUS_CANCELLED, 'id' => $registrationId]);

            if ($row['status'] === STATUS_CONFIRMED) {
                $oldest = $this->pdo->prepare(
                    "SELECT id FROM registrations
                     WHERE event_id = :eid AND status = 'waitlisted'
                     ORDER BY created_at ASC, id ASC LIMIT 1 FOR UPDATE"
                );
                $oldest->execute(['eid' => $row['event_id']]);
                $waitlisted = $oldest->fetch();
                if ($waitlisted) {
                    $promote = $this->pdo->prepare(
                        "UPDATE registrations SET status = 'confirmed' WHERE id = :id"
                    );
                    $promote->execute(['id' => $waitlisted['id']]);
                }
            }
            $this->pdo->commit();
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function updateStatus(int $registrationId, string $status): void
    {
        if (!in_array($status, REGISTRATION_STATUSES, true)) {
            throw new InvalidArgumentException('Invalid registration status.');
        }
        $this->pdo->beginTransaction();
        try {
            $reference = $this->pdo->prepare('SELECT event_id FROM registrations WHERE id = :id');
            $reference->execute(['id' => $registrationId]);
            $eventId = $reference->fetchColumn();
            if ($eventId === false) throw new DomainException('Registration not found.');

            $eventLock = $this->pdo->prepare('SELECT id FROM events WHERE id = :id FOR UPDATE');
            $eventLock->execute(['id' => (int) $eventId]);
            if ($eventLock->fetchColumn() === false) throw new DomainException('Event not found.');

            $row = $this->pdo->prepare(
                'SELECT r.event_id, r.status AS old_status, e.capacity FROM registrations r
                 JOIN events e ON e.id = r.event_id WHERE r.id = :id FOR UPDATE'
            );
            $row->execute(['id' => $registrationId]);
            $registration = $row->fetch();
            if (!$registration) throw new DomainException('Registration not found.');

            if ($status === STATUS_CONFIRMED) {
                $count = $this->pdo->prepare(
                    "SELECT COUNT(*) FROM registrations
                     WHERE event_id = :eid AND status = 'confirmed' AND id <> :id"
                );
                $count->execute(['eid' => $registration['event_id'], 'id' => $registrationId]);
                if ((int) $count->fetchColumn() >= (int) $registration['capacity']) {
                    throw new DomainException('This event is already full.');
                }
            }
            $update = $this->pdo->prepare('UPDATE registrations SET status = :status WHERE id = :id');
            $update->execute(['status' => $status, 'id' => $registrationId]);

            if ($registration['old_status'] === STATUS_CONFIRMED
                && $status !== STATUS_CONFIRMED) {
                $oldest = $this->pdo->prepare(
                    "SELECT id FROM registrations
                     WHERE event_id = :eid AND status = 'waitlisted'
                     ORDER BY created_at ASC, id ASC LIMIT 1 FOR UPDATE"
                );
                $oldest->execute(['eid' => $registration['event_id']]);
                $waitlisted = $oldest->fetch();
                if ($waitlisted) {
                    $promote = $this->pdo->prepare(
                        "UPDATE registrations SET status = 'confirmed' WHERE id = :id"
                    );
                    $promote->execute(['id' => $waitlisted['id']]);
                }
            }
            $this->pdo->commit();
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function confirmedCount(int $eventId): int
    {
        $statement = $this->pdo->prepare(
            "SELECT COUNT(*) FROM registrations WHERE event_id = :eid AND status = 'confirmed'"
        );
        $statement->execute(['eid' => $eventId]);
        return (int) $statement->fetchColumn();
    }

    public function confirmedTotal(): int
    {
        $statement = $this->pdo->prepare(
            'SELECT COUNT(*) FROM registrations WHERE status = :status'
        );
        $statement->execute(['status' => STATUS_CONFIRMED]);
        return (int) $statement->fetchColumn();
    }

    public function statsForUser(int $userId): array
    {
        $statement = $this->pdo->prepare(
            "SELECT
                SUM(CASE WHEN r.status IN ('confirmed','waitlisted') AND e.event_date >= CURDATE() THEN 1 ELSE 0 END) AS upcoming_count,
                SUM(CASE WHEN r.status = 'attended' THEN 1 ELSE 0 END) AS attended_count,
                SUM(CASE WHEN r.status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_count
             FROM registrations r
             JOIN events e ON e.id = r.event_id
             WHERE r.user_id = :uid"
        );
        $statement->execute(['uid' => $userId]);
        $stats = $statement->fetch();
        return [
            'upcoming' => (int) ($stats['upcoming_count'] ?? 0),
            'attended' => (int) ($stats['attended_count'] ?? 0),
            'cancelled' => (int) ($stats['cancelled_count'] ?? 0),
        ];
    }

    public function listAll(array $filters = [], int $page = 1, int $perPage = 10): array
    {
        if (!empty($filters['status']) && !in_array($filters['status'], REGISTRATION_STATUSES, true)) {
            throw new InvalidArgumentException('Invalid registration status.');
        }
        $offset = ($page - 1) * $perPage;
        $where = ['1=1'];
        $params = [];
        if (!empty($filters['event_id'])) {
            $where[] = 'r.event_id = :event_id';
            $params['event_id'] = (int) $filters['event_id'];
        }
        if (!empty($filters['status'])) {
            $where[] = 'r.status = :status';
            $params['status'] = $filters['status'];
        }
        if (!empty(trim((string) ($filters['search'] ?? '')))) {
            $where[] = '(u.name LIKE :search_name OR u.email LIKE :search_email)';
            $search = '%' . trim($filters['search']) . '%';
            $params['search_name'] = $search;
            $params['search_email'] = $search;
        }
        $whereSql = implode(' AND ', $where);
        $sql = "SELECT r.id, r.status, r.created_at, u.name AS user_name, u.email,
                       e.title AS event_title, e.event_date, reg.name AS region_name
                FROM registrations r
                JOIN users u ON u.id = r.user_id
                JOIN events e ON e.id = r.event_id
                JOIN venues v ON v.id = e.venue_id
                JOIN regions reg ON reg.id = v.region_id
                WHERE $whereSql
                ORDER BY r.created_at DESC, r.id DESC LIMIT :limit OFFSET :offset";
        $statement = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) $statement->bindValue(":$key", $value);
        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        $items = $statement->fetchAll();

        $countSql = "SELECT COUNT(*) FROM registrations r
                     JOIN users u ON u.id = r.user_id
                     JOIN events e ON e.id = r.event_id
                     JOIN venues v ON v.id = e.venue_id
                     JOIN regions reg ON reg.id = v.region_id
                     WHERE $whereSql";
        $count = $this->pdo->prepare($countSql);
        foreach ($params as $key => $value) $count->bindValue(":$key", $value);
        $count->execute();

        $confirmed = $this->pdo->prepare("SELECT COUNT(*) FROM registrations r JOIN users u ON u.id=r.user_id JOIN events e ON e.id=r.event_id JOIN venues v ON v.id=e.venue_id JOIN regions reg ON reg.id=v.region_id WHERE $whereSql AND r.status='confirmed'");
        $waitlisted = $this->pdo->prepare("SELECT COUNT(*) FROM registrations r JOIN users u ON u.id=r.user_id JOIN events e ON e.id=r.event_id JOIN venues v ON v.id=e.venue_id JOIN regions reg ON reg.id=v.region_id WHERE $whereSql AND r.status='waitlisted'");
        foreach ([$confirmed, $waitlisted] as $query) foreach ($params as $key => $value) $query->bindValue(":$key", $value);
        $confirmed->execute(); $waitlisted->execute();
        return ['items' => $items, 'total' => (int) $count->fetchColumn(),
                'confirmed' => (int) $confirmed->fetchColumn(),
                'waitlisted' => (int) $waitlisted->fetchColumn()];
    }

    public function recentActivity(int $limit = 10): array
    {
        $statement = $this->pdo->prepare(
            'SELECT r.status, r.created_at, u.name AS user_name, e.title AS event_title
             FROM registrations r JOIN users u ON u.id=r.user_id JOIN events e ON e.id=r.event_id
             ORDER BY r.created_at DESC, r.id DESC LIMIT :limit'
        );
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function dashboardStats(): array
    {
        $count = $this->pdo->prepare('SELECT COUNT(*) FROM registrations WHERE status = :status');
        $totalQuery = $this->pdo->prepare('SELECT COUNT(*) FROM registrations');
        $totalQuery->execute();
        $total = (int) $totalQuery->fetchColumn();
        $count->execute(['status' => STATUS_WAITLISTED]);
        $waitlisted = (int) $count->fetchColumn();
        $count->execute(['status' => STATUS_CONFIRMED]);
        $confirmed = (int) $count->fetchColumn();
        $count->execute(['status' => STATUS_CANCELLED]);
        $cancelled = (int) $count->fetchColumn();
        $count->execute(['status' => STATUS_ATTENDED]);
        $attended = (int) $count->fetchColumn();
        return [
            'total' => $total,
            'confirmed' => $confirmed,
            'waitlisted' => $waitlisted,
            'cancelled' => $cancelled,
            'attended' => $attended,
        ];
    }

    public function busiestUpcoming(int $limit = 8): array
    {
        $statement = $this->pdo->prepare(
            "SELECT e.id, e.title, e.capacity,
                    COALESCE(SUM(CASE WHEN r.status='confirmed' THEN 1 ELSE 0 END), 0) AS confirmed_count
             FROM events e LEFT JOIN registrations r ON r.event_id=e.id
             WHERE e.event_date >= CURDATE()
             GROUP BY e.id, e.title, e.capacity
             ORDER BY confirmed_count DESC, e.event_date ASC LIMIT :limit"
        );
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }
}