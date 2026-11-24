<?php

require __DIR__ . '/../config/config.php';
require __DIR__ . '/../config/database.php';

foreach (glob(APP_ROOT . '/app/helpers/*.php') ?: [] as $helper) {
    require_once $helper;
}
foreach (glob(APP_ROOT . '/app/models/*.php') ?: [] as $model) {
    require_once $model;
}
foreach (glob(APP_ROOT . '/app/controllers/*.php') ?: [] as $controller) {
    require_once $controller;
}

$pdo = getDatabaseConnection();
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'index';

$authController = new AuthController(new User($pdo), new Region($pdo));
$eventController = new EventController(
    new Event($pdo),
    new Region($pdo),
    new Category($pdo),
    new SavedEvent($pdo)
);

$homeController = new HomeController(
    new Event($pdo),
    new Region($pdo),
    new Category($pdo),
    new Registration($pdo)
);

$registrationController = new RegistrationController(
    new Registration($pdo),
    new Event($pdo)
);
$dashboardController = new DashboardController(
    new User($pdo),
    new Region($pdo),
    new Registration($pdo),
    new Event($pdo)
);
$adminController = new AdminController(
    new Event($pdo),
    new Category($pdo),
    new Venue($pdo),
    new Region($pdo),
    new Registration($pdo)
);

$knownRoute = true;

switch ($page) {
    case 'home':
        if ($action === 'index') {
            $homeController->index();
        } elseif ($action === 'how-it-works') {
            $homeController->index();
        } else {
            $knownRoute = false;
        }
        break;
    case 'auth':
        if ($action === 'register') {
            $authController->register();
        } elseif ($action === 'login') {
            $authController->login();
        } elseif ($action === 'logout') {
            $authController->logout();
        } else {
            $knownRoute = false;
        }
        break;
    case 'events':
        if ($action === 'index') {
            $eventController->index();
        } elseif ($action === 'show') {
            $eventController->show();
        } elseif ($action === 'saved') {
            $eventController->saved();
        } elseif ($action === 'saved-import') {
            $eventController->importSaved();
        } else {
            $knownRoute = false;
        }
        break;
    case 'registrations':
        if ($action === 'store') $registrationController->store();
        elseif ($action === 'cancel') $registrationController->cancel();
        else $knownRoute=false;
        break;
    case 'dashboard':
        if ($action === 'index') $dashboardController->index();
        elseif ($action === 'registrations') $dashboardController->registrations();
        elseif ($action === 'profile') $dashboardController->profile();
        elseif ($action === 'updateProfile') $dashboardController->updateProfile();
        elseif ($action === 'changePassword') $dashboardController->changePassword();
        else $knownRoute=false;
        break;
    case 'admin':
        if ($action === 'dashboard') $adminController->dashboard();
        elseif ($action === 'events') $adminController->events();
        elseif ($action === 'eventCreate') $adminController->eventCreate();
        elseif ($action === 'eventStore') $adminController->eventStore();
        elseif ($action === 'eventEdit') $adminController->eventEdit();
        elseif ($action === 'eventUpdate') $adminController->eventUpdate();
        elseif ($action === 'eventDelete') $adminController->eventDelete();
        elseif ($action === 'categories') $adminController->categories();
        elseif ($action === 'categoryStore') $adminController->categoryStore();
        elseif ($action === 'categoryUpdate') $adminController->categoryUpdate();
        elseif ($action === 'categoryDelete') $adminController->categoryDelete();
        elseif ($action === 'venues') $adminController->venues();
        elseif ($action === 'venueStore') $adminController->venueStore();
        elseif ($action === 'venueUpdate') $adminController->venueUpdate();
        elseif ($action === 'venueDelete') $adminController->venueDelete();
        elseif ($action === 'registrations') $adminController->registrations();
        elseif ($action === 'registrationStatus') $adminController->registrationStatus();
        else $knownRoute=false;
        break;
    default:
        $knownRoute = false;
        break;
}

if (!$knownRoute) {
    http_response_code(404);
    require APP_ROOT . '/app/views/errors/404.php';
}