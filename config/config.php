<?php

define('APP_NAME', 'Tukutane');
define('APP_DEBUG', true);
define('APP_ROOT', dirname(__DIR__));
define('BASE_URL', '/event-platform/public');
define('UPLOAD_URL', BASE_URL . '/images/events');

define('STATUS_CONFIRMED', 'confirmed');
define('STATUS_WAITLISTED', 'waitlisted');
define('STATUS_CANCELLED', 'cancelled');
define('STATUS_ATTENDED', 'attended');
define('REGISTRATION_STATUSES', [
    STATUS_CONFIRMED, STATUS_WAITLISTED, STATUS_CANCELLED, STATUS_ATTENDED,
]);

ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', APP_ROOT . '/php-error.log');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    'httponly' => true,
    'samesite' => 'Lax',
]);
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}