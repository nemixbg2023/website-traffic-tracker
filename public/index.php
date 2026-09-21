<?php

// --- 1. Load Composer autoloader (automatically finds App\ classes) ---

require_once __DIR__ . '/../vendor/autoload.php';

use App\Container\Container;
use App\Repository\PageViewRepositoryInterface;
use App\Repository\MySqlPageViewRepository;
use App\Router;

// --- 2. Build and configure the container ---

$container = new Container();

// Tell the container how to build a PDO connection
$container->bind(PDO:: class, function ($container) {
    $pdo = new PDO(
        'mysql:host=db;dbname=traffic_tracker;charset=utf8mb4',
        'tracker_user',
        'trackerpass'
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
});

// Tell the container which concrete class to use for this interface
$container->bind(PageViewRepositoryInterface::class, function($container) {
    return new MySqlPageViewRepository($container->get(PDO::class));
});

// --- 3. Route the request based on URI path ---

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$router = new Router($container);
$router->dispatch($path, file_get_contents('php://input'));
