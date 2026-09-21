<?php

namespace Tests;

use App\Container\Container;
use App\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    public function test_returns_404_for_unknown_path(): void
    {
        $container = new Container();
        $router = new Router($container);

        ob_start();
        $router->dispatch('/nonexistent', '{}');
        $output = ob_get_clean();

        $this->assertSame(404, http_response_code());
        $this->assertStringContainsString('404 Not Found', $output);
    }

    public function test_returns_500_when_controller_throws_an_exception(): void
    {
        // Create a container that will throw when asked to build ANYTHING,
        // simulating a broken dependency (e.g. database connection failure)
        $container = $this->createMock(Container::class);
        $container->method('get')->willThrowException(new \RuntimeException('Simulated failure'));

        $router = new Router($container);

        ob_start();
        $router->dispatch('/dashboard', '{}');
        $output = ob_get_clean();

        $this->assertSame(500, http_response_code());
        $this->assertStringContainsString('Internal server error', $output);
    }
}