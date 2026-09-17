<?php

// --- 1. Manually load our classes (no Composer autoload yet) ---

require_once __DIR__ . '/../src/Repository/PageViewRepository.php';
require_once __DIR__ . '/../src/Service/TrackingService.php';
require_once __DIR__ . '/../src/Controller/TrackController.php';
require_once __DIR__ . '/../src/Controller/DashboardController.php';

use App\Repository\PageViewRepository;
use App\Service\TrackingService;
use App\Controller\TrackController;
use App\Controller\DashboardController;

// --- 2. Build shared dependencies ---

$pdo = new PDO(
    'mysql:host=db;dbname=traffic_tracker;charset=utf8mb4',
    'tracker_user',
    'trackerpass'
);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$repository = new PageViewRepository($pdo);
$service = new TrackingService();

// --- 3. Route the request based on URI path ---

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === '/track') {
    $controller = new TrackController($repository, $service);
    $controller->handle();
} elseif ($path === '/dashboard') {
    $controller = new DashboardController($repository);
    $controller->handle();
} else {
    http_response_code(404);
    echo '404 Not Found';
}