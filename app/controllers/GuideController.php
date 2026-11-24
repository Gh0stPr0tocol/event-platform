<?php

class GuideController
{
    public function findEvent(): void
    {
        $title = 'Find an event';
        require APP_ROOT . '/app/views/guides/find-event.php';
    }

    public function registerFree(): void
    {
        $title = 'Register free';
        require APP_ROOT . '/app/views/guides/register-free.php';
    }

    public function showUp(): void
    {
        $title = 'Show up';
        require APP_ROOT . '/app/views/guides/show-up.php';
    }
}
