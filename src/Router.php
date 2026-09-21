<?php

namespace App;

use App\Container\Container;
use App\Controller\TrackController;
use App\Controller\DashboardController;

class Router
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function dispatch(string $path, string $rawBody): void
    {
        try {
            if ($path === '/track') {
                $controller = $this->container->get(TrackController::class);
                $controller->handle(file_get_contents('php://input'));
            } elseif ($path === '/dashboard') {
                $controller = $this->container->get(DashboardController::class);
                $controller->handle();
            } else {
                http_response_code(404);
                echo '404 Not Found';
            }
        } catch (\Throwable $e) {
            // Log the real error for developers, but never expose internals to the client
            error_log($e->getMessage());

            http_response_code(500);
            echo json_encode(['error' => 'Internal server error']);
        }
    }
}
