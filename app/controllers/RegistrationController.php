<?php

class RegistrationController
{
    public function __construct(private Registration $registrations, private Event $events) {}

    public function store(): void
    {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); redirect('?page=events&action=index'); }
        if (!verifyCsrfToken()) { setFlash('error','Your session expired. Please try again.'); redirect('?page=events&action=index'); }
        $eventId=filter_input(INPUT_POST,'event_id',FILTER_VALIDATE_INT);
        if (!$eventId) { setFlash('error','Invalid event.'); redirect('?page=events&action=index'); }
        $event=$this->events->find($eventId);
        if (!$event) { setFlash('error','Event not found.'); redirect('?page=events&action=index'); }
        if ($event['event_date']<date('Y-m-d')) { setFlash('error','This event is already past.'); redirect('?page=events&action=show&id='.$eventId); }
        try {
            $result=$this->registrations->create((int)currentUser()['id'],$eventId);
            setFlash('success',$result['status']===STATUS_CONFIRMED
                ? 'Registration confirmed.'
                : 'Event is full, you have been added to the waitlist.');
        } catch (DomainException $exception) {
            setFlash('error',$exception->getMessage());
        } catch (Throwable $exception) {
            error_log($exception->getMessage());
            setFlash('error','We could not complete your registration. Please try again.');
        }
        redirect('?page=events&action=show&id='.$eventId);
    }

    public function cancel(): void
    {
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); redirect('?page=dashboard&action=registrations'); }
        if (!verifyCsrfToken()) { setFlash('error','Your session expired. Please try again.'); redirect('?page=dashboard&action=registrations'); }
        $id=filter_input(INPUT_POST,'registration_id',FILTER_VALIDATE_INT);
        if (!$id) { setFlash('error','Invalid registration.'); redirect('?page=dashboard&action=registrations'); }
        try {
            $this->registrations->cancel($id,(int)currentUser()['id']);
            setFlash('success','Registration cancelled.');
        } catch (DomainException $exception) {
            setFlash('error',$exception->getMessage());
        } catch (Throwable $exception) {
            error_log($exception->getMessage());
            setFlash('error','We could not cancel this registration.');
        }
        redirect('?page=dashboard&action=registrations');
    }
}