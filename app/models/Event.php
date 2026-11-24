<?php

class Event
{
    public function __construct(private PDO $pdo) {}

    public function upcoming(int $limit = 3): array
    {
        $statement = $this->pdo->prepare(
            'SELECT e.*, c.name AS category_name, v.name AS venue_name, r.name AS region_name,
                    GREATEST(e.capacity - (SELECT COUNT(*) FROM registrations
                     WHERE event_id = e.id AND status = "confirmed"), 0) AS seats_remaining
             FROM events e
             JOIN categories c ON c.id = e.category_id
             JOIN venues v ON v.id = e.venue_id
             JOIN regions r ON r.id = v.region_id
             WHERE e.event_date >= CURDATE()
             ORDER BY e.event_date ASC, e.start_time ASC
             LIMIT :limit'
        );
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function nextUpcoming(): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT e.*, c.name AS category_name, v.name AS venue_name, r.name AS region_name,
                    GREATEST(e.capacity - (SELECT COUNT(*) FROM registrations
                     WHERE event_id = e.id AND status = "confirmed"), 0) AS seats_remaining
             FROM events e
             JOIN categories c ON c.id = e.category_id
             JOIN venues v ON v.id = e.venue_id
             JOIN regions r ON r.id = v.region_id
             WHERE TIMESTAMP(e.event_date, e.start_time) >= NOW()
             ORDER BY e.event_date ASC, e.start_time ASC
             LIMIT 1'
        );
        $statement->execute();
        $event = $statement->fetch();
        return $event ?: null;
    }

    public function homeStats(): array
    {
        $statement = $this->pdo->prepare(
            'SELECT COUNT(*) AS upcoming_events, COUNT(DISTINCT v.region_id) AS counties_with_events
             FROM events e
             JOIN venues v ON v.id = e.venue_id
             WHERE e.event_date >= CURDATE()'
        );
        $statement->execute();
        $stats = $statement->fetch();

        return [
            'upcoming_events' => (int) $stats['upcoming_events'],
            'counties_with_events' => (int) $stats['counties_with_events'],
        ];
    }

    public function search(string $keyword = '', int|string $regionId = '', int|string $categoryId = ''): array
    {
        $sql = 'SELECT e.*, c.name AS category_name, v.name AS venue_name, r.name AS region_name,
                       GREATEST(e.capacity - (SELECT COUNT(*) FROM registrations
                        WHERE event_id = e.id AND status = "confirmed"), 0) AS seats_remaining
                FROM events e
                JOIN categories c ON c.id = e.category_id
                JOIN venues v ON v.id = e.venue_id
                JOIN regions r ON r.id = v.region_id
                WHERE e.event_date >= CURDATE()';
        $params = [];

        if ($keyword !== '') {
            $sql .= ' AND (e.title LIKE :keyword_title OR e.description LIKE :keyword_description)';
            $params['keyword_title'] = '%' . $keyword . '%';
            $params['keyword_description'] = '%' . $keyword . '%';
        }
        if ($regionId !== '' && ctype_digit((string) $regionId)) {
            $sql .= ' AND r.id = :region_id';
            $params['region_id'] = (int) $regionId;
        }
        if ($categoryId !== '' && ctype_digit((string) $categoryId)) {
            $sql .= ' AND c.id = :category_id';
            $params['category_id'] = (int) $categoryId;
        }

        $sql .= ' ORDER BY e.event_date ASC, e.start_time ASC';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    public function filteredSearch(
        string $keyword = '',
        int|string $regionId = '',
        int|string $categoryId = '',
        string $dateFrom = '',
        string $dateTo = '',
        int $page = 1,
        int $perPage = 9
    ): array {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $where = ['e.event_date >= CURDATE()'];
        $params = [];

        if ($keyword !== '') {
            $where[] = '(e.title LIKE :keyword_title OR e.description LIKE :keyword_description)';
            $params['keyword_title'] = '%' . $keyword . '%';
            $params['keyword_description'] = '%' . $keyword . '%';
        }
        if ($regionId !== '' && ctype_digit((string) $regionId) && (int) $regionId > 0) {
            $where[] = 'r.id = :region_id';
            $params['region_id'] = (int) $regionId;
        }
        if ($categoryId !== '' && ctype_digit((string) $categoryId) && (int) $categoryId > 0) {
            $where[] = 'c.id = :category_id';
            $params['category_id'] = (int) $categoryId;
        }
        if ($dateFrom !== '' && $this->isValidIsoDate($dateFrom)) {
            $where[] = 'e.event_date >= :date_from';
            $params['date_from'] = $dateFrom;
        }
        if ($dateTo !== '' && $this->isValidIsoDate($dateTo)) {
            $where[] = 'e.event_date <= :date_to';
            $params['date_to'] = $dateTo;
        }

        $join = ' FROM events e
                  JOIN categories c ON c.id = e.category_id
                  JOIN venues v ON v.id = e.venue_id
                  JOIN regions r ON r.id = v.region_id
                  WHERE ' . implode(' AND ', $where);

        $countStatement = $this->pdo->prepare('SELECT COUNT(*)' . $join);
        $countStatement->execute($params);
        $total = (int) $countStatement->fetchColumn();
        $pages = max(1, (int) ceil($total / $perPage));
        $page = min($page, $pages);
        $offset = ($page - 1) * $perPage;

        $sql = 'SELECT e.*, c.name AS category_name, v.name AS venue_name, r.name AS region_name,
                       GREATEST(e.capacity - (SELECT COUNT(*) FROM registrations
                        WHERE event_id = e.id AND status = "confirmed"), 0) AS seats_remaining'
             . $join . ' ORDER BY e.event_date ASC, e.start_time ASC
                        LIMIT :limit OFFSET :offset';
        $statement = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $statement->bindValue(':' . $key, $value);
        }
        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return [
            'items' => $statement->fetchAll(),
            'total' => $total,
            'pages' => $pages,
            'page' => $page,
        ];
    }

    private function isValidIsoDate(string $value): bool
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        $errors = DateTimeImmutable::getLastErrors();
        return $date !== false
            && $date->format('Y-m-d') === $value
            && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0));
    }

    public function list(array $filters = [], int $page = 1, int $perPage = 10): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $where = ['1=1'];
        $params = [];

        if (!empty(trim((string) ($filters['search'] ?? '')))) {
            $where[] = 'e.title LIKE :search';
            $params['search'] = '%' . trim((string) $filters['search']) . '%';
        }
        if (!empty($filters['category_id'])) {
            $where[] = 'e.category_id = :category_id';
            $params['category_id'] = (int) $filters['category_id'];
        }
        if (!empty($filters['region_id'])) {
            $where[] = 'v.region_id = :region_id';
            $params['region_id'] = (int) $filters['region_id'];
        }
        if (($filters['time'] ?? '') === 'upcoming') {
            $where[] = 'e.event_date >= CURDATE()';
        }
        if (($filters['time'] ?? '') === 'past') {
            $where[] = 'e.event_date < CURDATE()';
        }

        $whereSql = implode(' AND ', $where);
        $base = ' FROM events e
                  JOIN categories c ON c.id = e.category_id
                  JOIN venues v ON v.id = e.venue_id
                  JOIN regions r ON r.id = v.region_id
                  WHERE ' . $whereSql;

        $count = $this->pdo->prepare('SELECT COUNT(*)' . $base);
        foreach ($params as $key => $value) {
            $count->bindValue(':' . $key, $value);
        }
        $count->execute();

        $total = (int) $count->fetchColumn();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $sql = 'SELECT e.*, c.name AS category_name, v.name AS venue_name, r.name AS region_name,
                       GREATEST(e.capacity - (SELECT COUNT(*) FROM registrations
                        WHERE event_id = e.id AND status = "confirmed"), 0) AS seats_remaining'
             . $base . ' ORDER BY e.event_date ASC, e.start_time ASC LIMIT :limit OFFSET :offset';
        $statement = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $statement->bindValue(':' . $key, $value);
        }
        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return [
            'items' => $statement->fetchAll(),
            'total' => $total,
            'page' => $page,
            'pages' => $totalPages,
        ];
    }

    public function find(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT e.*, c.name AS category_name, v.name AS venue_name, v.address, r.name AS region_name,
                    COALESCE(confirmed.confirmed_count, 0) AS confirmed_count,
                    GREATEST(e.capacity - COALESCE(confirmed.confirmed_count, 0), 0) AS seats_remaining
             FROM events e
             JOIN categories c ON c.id = e.category_id
             JOIN venues v ON v.id = e.venue_id
             JOIN regions r ON r.id = v.region_id
             LEFT JOIN (
                 SELECT event_id, COUNT(*) AS confirmed_count
                 FROM registrations
                 WHERE status = "confirmed"
                 GROUP BY event_id
             ) confirmed ON confirmed.event_id = e.id
             WHERE e.id = :id LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO events (title, description, event_date, start_time, capacity, image, category_id, venue_id, created_by)
             VALUES (:title, :description, :event_date, :start_time, :capacity, :image, :category_id, :venue_id, :created_by)'
        );
        $statement->execute($data);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE events SET title=:title, description=:description, event_date=:event_date,
             start_time=:start_time, capacity=:capacity, image=:image, category_id=:category_id,
             venue_id=:venue_id WHERE id=:id'
        );
        $statement->execute([
            'title' => $data['title'],
            'description' => $data['description'],
            'event_date' => $data['event_date'],
            'start_time' => $data['start_time'],
            'capacity' => $data['capacity'],
            'image' => $data['image'],
            'category_id' => $data['category_id'],
            'venue_id' => $data['venue_id'],
            'id' => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare('DELETE FROM events WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    public function registrationCount(int $id): int
    {
        $statement = $this->pdo->prepare('SELECT COUNT(*) FROM registrations WHERE event_id = :id');
        $statement->execute(['id' => $id]);
        return (int) $statement->fetchColumn();
    }

    public function confirmedCount(int $id): int
    {
        $statement = $this->pdo->prepare(
            "SELECT COUNT(*) FROM registrations WHERE event_id = :id AND status = 'confirmed'"
        );
        $statement->execute(['id' => $id]);
        return (int) $statement->fetchColumn();
    }

    public function localUpcoming(int $regionId, int $excludeUserId, int $limit = 4): array
    {
        $statement = $this->pdo->prepare(
            'SELECT e.*, c.name AS category_name, v.name AS venue_name, r.name AS region_name,
                    GREATEST(e.capacity - (SELECT COUNT(*) FROM registrations
                     WHERE event_id = e.id AND status = "confirmed"), 0) AS seats_remaining
             FROM events e
             JOIN categories c ON c.id = e.category_id
             JOIN venues v ON v.id = e.venue_id
             JOIN regions r ON r.id = v.region_id
             WHERE e.event_date >= CURDATE()
               AND v.region_id = :region
               AND NOT EXISTS (
                   SELECT 1 FROM registrations x
                   WHERE x.event_id = e.id AND x.user_id = :uid
                     AND x.status IN ("confirmed", "waitlisted")
               )
             ORDER BY e.event_date ASC, e.start_time ASC
             LIMIT :limit'
        );
        $statement->bindValue(':region', $regionId, PDO::PARAM_INT);
        $statement->bindValue(':uid', $excludeUserId, PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function statistics(): array
    {
        $totalQuery = $this->pdo->prepare('SELECT COUNT(*) FROM events');
        $totalQuery->execute();
        $total = (int) $totalQuery->fetchColumn();

        $upcomingQuery = $this->pdo->prepare('SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()');
        $upcomingQuery->execute();
        $upcoming = (int) $upcomingQuery->fetchColumn();

        $capacityQuery = $this->pdo->prepare(
            'SELECT COALESCE(SUM(capacity), 0) FROM events WHERE event_date >= CURDATE()'
        );
        $capacityQuery->execute();
        $capacity = (int) $capacityQuery->fetchColumn();

        $filledQuery = $this->pdo->prepare(
            "SELECT COUNT(*) FROM registrations r JOIN events e ON e.id = r.event_id
             WHERE e.event_date >= CURDATE() AND r.status = 'confirmed'"
        );
        $filledQuery->execute();
        $filled = (int) $filledQuery->fetchColumn();
        return [
            'total' => $total,
            'upcoming' => $upcoming,
            'capacity' => $capacity,
            'filled' => $filled,
            'percent' => $capacity ? round($filled / $capacity * 100) : 0,
        ];
    }
}
