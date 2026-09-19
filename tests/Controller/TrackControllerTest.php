<?php

namespace Tests\Controller;

use App\Controller\TrackController;
use App\Repository\PageViewRepositoryInterface;
use App\Service\TrackingService;
use PHPUnit\Framework\TestCase;

class TrackControllerTest extends TestCase
{
    protected function setUp(): void
    {
        // Reset superglobals before each test, so tests don't leak state into each other
        $_COOKIE = [];
        $_SERVER['HTTP_USER_AGENT'] = 'TestAgent/1.0';
    }

    public function test_returns_400_when_page_url_is_missing(): void
    {
        // Mock the repository - we don't want a real database involved in this test
        $repository = $this->createMock(PageViewRepositoryInterface::class);
        $repository->expects($this->never())->method('save');

        $service = new TrackingService();

        $controller = new TrackController($repository, $service);

        // Simulate an empty request body (no page_url sent)
        $controller->handle('{}');

        $this->assertSame(400, http_response_code());
    }

    public function test_saves_page_view_when_page_url_is_provided(): void
    {
        $repository = $this->createMock(PageViewRepositoryInterface::class);

        $repository->expects($this->once())
            ->method('save')
            ->with(
                'https://example.com/test',
                'https://google.com',
                $this->isType('string'),
                'TestAgent/1.0'
            );
        
            $service = new TrackingService();

            $controller = new TrackController($repository, $service);

            $requestBody = json_encode([
                'page_url' => 'https://example.com/test',
                'referrer' => 'https://google.com',
            ]);

            $controller->handle($requestBody);

            $this->assertSame(204, http_response_code());
    }
}