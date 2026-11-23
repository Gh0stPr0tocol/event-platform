<?php

class HomeController
{
    public function __construct(
        private Event $events,
        private Region $regions,
        private Category $categories
    ) {}

    public function index(): void
    {
        $upcomingEvents = $this->events->upcoming(6);
        $regions = $this->regions->all();
        $categories = $this->categories->all();
        require APP_ROOT . '/app/views/events/index.php';
    }
}
