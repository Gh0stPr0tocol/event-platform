<?php

class HomeController
{
    public function __construct(
        private Event $events,
        private Region $regions,
        private Category $categories,
        private Registration $registrations
    ) {}

    public function index(): void
    {
        $upcomingEvents = $this->events->upcoming(6);
        $regions = $this->regions->all();
        $categories = $this->categories->all();
        $homeStats = $this->events->homeStats();
        $homeStats['confirmed_registrations'] = $this->registrations->confirmedTotal();
        $nextEvent = $this->events->nextUpcoming();
        require APP_ROOT . '/app/views/events/index.php';
    }
}
