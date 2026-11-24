<?php

class EventController
{
    public function __construct(
        private Event $events,
        private Region $regions,
        private Category $categories,
        private SavedEvent $savedEvents
    ) {}

    public function home(): void
    {
        $upcomingEvents = $this->events->upcoming(6);
        $regions = $this->regions->all();
        $categories = $this->categories->all();
        require APP_ROOT . '/app/views/events/index.php';
    }

    public function index(): void
    {
        $keyword = $this->queryString('keyword', 200);
        $regionId = $this->positiveId($_GET['region_id'] ?? '');
        $categoryValue = $_GET['category_id'] ?? $_GET['category'] ?? '';
        $categoryId = $this->positiveId($categoryValue);
        $dateFrom = $this->queryString('date_from', 50);
        $dateTo = $this->queryString('date_to', 50);
        $filterErrors = [];
        $dateFromFilter = $dateFrom;
        $dateToFilter = $dateTo;

        if ($dateFrom !== '' && !$this->isValidDate($dateFrom)) {
            $filterErrors['date_from'] = 'Enter a valid start date.';
            $dateFromFilter = '';
        }
        if ($dateTo !== '' && !$this->isValidDate($dateTo)) {
            $filterErrors['date_to'] = 'Enter a valid end date.';
            $dateToFilter = '';
        }
        if (
            $dateFromFilter !== ''
            && $dateToFilter !== ''
            && $dateToFilter < $dateFromFilter
        ) {
            $filterErrors['date_to'] = 'The end date must be on or after the start date.';
            $dateFromFilter = '';
            $dateToFilter = '';
        }

        $savedOnly = $this->queryString('saved', 5) === '1';
        $activeFilterBaseCount = count(array_filter(
            [$keyword, $regionId, $categoryId, $dateFrom, $dateTo],
            static fn (string $value): bool => $value !== ''
        ));
        $activeFilterCount = $activeFilterBaseCount + ($savedOnly ? 1 : 0);
        $page = (int) ($this->positiveId($_GET['list_page'] ?? '') ?: 1);
        $perPage = 9;
        $eventPage = $this->events->filteredSearch(
            $keyword,
            $regionId,
            $categoryId,
            $dateFromFilter,
            $dateToFilter,
            $page,
            $perPage
        );
        $events = $eventPage['items'];
        $eventTotal = $eventPage['total'];
        $eventPages = $eventPage['pages'];
        $currentPage = $eventPage['page'];
        $firstResult = $eventTotal === 0 ? 0 : (($currentPage - 1) * $perPage) + 1;
        $lastResult = min($currentPage * $perPage, $eventTotal);
        $regions = $this->regions->all();
        $categories = $this->categories->all();
        require APP_ROOT . '/app/views/events/index.php';
    }

    public function saved(): void
    {
        if (!isLoggedIn()) {
            $this->jsonResponse(['error' => 'Sign in to sync your saved events.'], 401);
            return;
        }

        try {
            $userId = (int) currentUser()['id'];
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $this->jsonResponse(['event_ids' => $this->savedEvents->idsForUser($userId)]);
                return;
            }
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Allow: GET, POST');
                $this->jsonResponse(['error' => 'Method not allowed.'], 405);
                return;
            }
            if (!verifyCsrfToken()) {
                $this->jsonResponse(['error' => 'Your session expired. Refresh and try again.'], 419);
                return;
            }

            $rawEventId = $_POST['event_id'] ?? null;
            $eventId = is_string($rawEventId) || is_int($rawEventId)
                ? filter_var($rawEventId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
                : false;
            $rawSaved = $_POST['saved'] ?? null;
            if (
                $eventId === false
                || !is_string($rawSaved)
                || !in_array($rawSaved, ['true', 'false', '1', '0'], true)
            ) {
                $this->jsonResponse(['error' => 'Choose a valid event to save.'], 422);
                return;
            }
            $saved = in_array($rawSaved, ['true', '1'], true);
            if ($saved && !$this->events->find((int) $eventId)) {
                $this->jsonResponse(['error' => 'That event is no longer available.'], 404);
                return;
            }

            $this->savedEvents->setSaved((int) $userId, (int) $eventId, $saved);
            $this->jsonResponse(['saved' => $saved]);
        } catch (Throwable $exception) {
            error_log($exception->getMessage());
            $this->jsonResponse(['error' => 'Saved events could not be updated. Please try again.'], 500);
        }
    }

    public function importSaved(): void
    {
        if (!isLoggedIn()) {
            $this->jsonResponse(['error' => 'Sign in to sync your saved events.'], 401);
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Allow: POST');
            $this->jsonResponse(['error' => 'Method not allowed.'], 405);
            return;
        }
        if (!verifyCsrfToken()) {
            $this->jsonResponse(['error' => 'Your session expired. Refresh and try again.'], 419);
            return;
        }

        $rawIds = $_POST['event_ids'] ?? [];
        if (!is_array($rawIds) || count($rawIds) > 200) {
            $this->jsonResponse(['error' => 'The saved-event list is invalid.'], 422);
            return;
        }

        $eventIds = [];
        foreach ($rawIds as $rawId) {
            $eventId = is_string($rawId) || is_int($rawId)
                ? filter_var($rawId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
                : false;
            if ($eventId === false) {
                $this->jsonResponse(['error' => 'The saved-event list is invalid.'], 422);
                return;
            }
            $eventIds[] = (int) $eventId;
        }

        try {
            $this->savedEvents->mergeForUser((int) currentUser()['id'], array_values(array_unique($eventIds)));
            $this->jsonResponse(['event_ids' => $this->savedEvents->idsForUser((int) currentUser()['id'])]);
        } catch (Throwable $exception) {
            error_log($exception->getMessage());
            $this->jsonResponse(['error' => 'Saved events could not be synced. Please try again.'], 500);
        }
    }

    public function show(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $event = $id ? $this->events->find($id) : null;
        if (!$event) {
            http_response_code(404);
            require APP_ROOT . '/app/views/errors/404.php';
            return;
        }
        require APP_ROOT . '/app/views/events/show.php';
    }

    private function queryString(string $key, int $maxLength = 200): string
    {
        $value = $_GET[$key] ?? '';
        return is_string($value) ? substr(trim($value), 0, $maxLength) : '';
    }

    private function positiveId(mixed $value): string
    {
        if (!is_string($value) && !is_int($value)) {
            return '';
        }
        $id = filter_var(
            $value,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );
        return $id === false ? '' : (string) $id;
    }

    private function isValidDate(string $value): bool
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        $errors = DateTimeImmutable::getLastErrors();
        return $date !== false
            && $date->format('Y-m-d') === $value
            && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0));
    }

    private function jsonResponse(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        echo json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
    }
}