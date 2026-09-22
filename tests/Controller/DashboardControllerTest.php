<?php

namespace Tests\Controller;

use App\Repository\PageViewRepositoryInterface;
use App\Controller\DashboardController;
use App\Service\PeriodResolver;
use App\View\DashboardView;
use Override;
use PHPUnit\Framework\TestCase;

class DashboardControllerTest extends TestCase
{
    protected function setUp(): void
    {
        $_GET = [];
    }

    public function test_renders_page_view_table_with_repository_data(): void
    {
        $repository = $this->createMock(PageViewRepositoryInterface::class);
        $repository->method('getAggregatedStats')->willReturn([
            [
                'page_url' => 'https://example.com/page-1',
                'total_views' => 5,
                'unique_visitors' => 3,
            ],
        ]);

        $controller = new DashboardController($repository, new DashboardView(), new PeriodResolver());

        // Start capturing output instead of letting it print directly
        ob_start();
        $controller->handle();
        $html = ob_get_clean();

        $this->assertStringContainsString('https://example.com/page-1', $html);
        $this->assertStringContainsString('5', $html);
        $this->assertStringContainsString('3', $html);
    }

    public function test_escapes_page_url_to_prevent_xss(): void
    {
        $repository = $this->createMock(PageViewRepositoryInterface::class);
        $repository->method('getAggregatedStats')->willReturn([
            [
                'page_url' => '<script>alert("xss")</script>',
                'total_views' => 1,
                'unique_visitors' => 1,
            ],
        ]);

        $controller = new DashboardController($repository, new DashboardView(), new PeriodResolver());

        ob_start();
        $controller->handle();
        $html = ob_get_clean();

        // The raw <script> tag must NOT appear unscapped in the output
        $this->assertStringNotContainsString('<script>alert("xss")</script>', $html);
        // It should appear as an escaped, harmless string instead
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    public function test_passes_period_from_query_string_to_repository(): void
    {
        $repository = $this->createMock(PageViewRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('getAggregatedStats')
            ->with(
                $this->isInstanceOf(\DateTimeImmutable::class),
                $this->isInstanceOf(\DateTimeImmutable::class)
            )
            ->willReturn([]);

        $_GET['period'] = '7days';

        $controller = new DashboardController($repository, new DashboardView(), new PeriodResolver());

        ob_start();
        $controller->handle();
        ob_get_clean();
    }

    public function test_defaults_to_display_today_when_no_period_given(): void
    {
        $repository = $this->createMock(PageViewRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('getAggregatedStats')
            ->willReturn([]);

        $controller = new DashboardController($repository, new DashboardView(), new PeriodResolver());

        ob_start();
        $controller->handle();
        $html = ob_get_clean();

        $this->assertStringContainsString('value="today" selected', $html);
    }
}
