<?php

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(): bool
{
    $submitted = $_POST['csrf_token'] ?? '';
    return is_string($submitted)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $submitted);
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return is_array($flash) ? $flash : null;
}

function formatDate(string $date): string
{
    $timestamp = strtotime($date);
    return $timestamp ? date('D, j M Y', $timestamp) : $date;
}

function formatTime(string $time): string
{
    $timestamp = strtotime($time);
    return $timestamp ? date('g:i A', $timestamp) : $time;
}

function setOldInput(array $input): void
{
    $_SESSION['old_input'] = $input;
}

function clearOldInput(): void
{
    unset($_SESSION['old_input']);
}

function isActiveNav(string $page, ?array $actions = null): bool
{
    $currentPage = isset($_GET['page']) && is_string($_GET['page'])
        ? $_GET['page']
        : 'home';
    $currentAction = isset($_GET['action']) && is_string($_GET['action'])
        ? $_GET['action']
        : 'index';

    if ($currentPage !== $page) {
        return false;
    }

    return $actions === null || in_array($currentAction, $actions, true);
}