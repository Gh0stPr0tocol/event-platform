<?php

class EventController
{
    public function __construct(
        private Event $events,
        private Region $regions,
        private Category $categories
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
        $keyword = trim($_GET['keyword'] ?? '');
        $regionId = trim($_GET['region_id'] ?? '');
        $categoryValue = $_GET['category_id'] ?? $_GET['category'] ?? '';
        $categoryId = is_string($categoryValue) ? trim($categoryValue) : '';
        $dateFrom = trim($_GET['date_from'] ?? '');
        $dateTo = trim($_GET['date_to'] ?? '');
        $page = max(1, (int) ($_GET['list_page'] ?? 1));
        $eventPage = $this->events->filteredSearch(
            $keyword,
            $regionId,
            $categoryId,
            $dateFrom,
            $dateTo,
            $page,
            9
        );
        $events = $eventPage['items'];
        $eventTotal = $eventPage['total'];
        $eventPages = $eventPage['pages'];
        $currentPage = $eventPage['page'];
        $regions = $this->regions->all();
        $categories = $this->categories->all();
        require APP_ROOT . '/app/views/events/index.php';
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
}