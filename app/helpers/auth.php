<?php

function isLoggedIn(): bool
{
    return isset($_SESSION['user']);
}

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        setFlash('warning', 'Please log in to continue.');
        redirect('?page=auth&action=login');
    }
}

function requireAdmin(): void
{
    requireLogin();
    if (($_SESSION['user']['role'] ?? '') !== 'admin') {
        http_response_code(403);
        setFlash('error', 'You do not have permission to access that page.');
        redirect('?page=home&action=index');
    }
}