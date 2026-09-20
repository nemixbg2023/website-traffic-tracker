<?php

namespace Tests\View;

use App\View\DashboardView;
use PHPUnit\Framework\TestCase;

class DashboardViewTest extends TestCase
{
    public function test_renders_empty_table_when_no_stats(): void
    {
        $view = new DashboardView();

        $html = $view->render([]);

        $this->assertStringContainsString('<table', $html);
        $this->assertStringContainsString('Traffic Tracker Dashboard', $html);
    }

    public function test_renders_row_for_each_stat_entry(): void
    {
        $view = new DashboardView();

        $html = $view->render([
            [
                'page_url' => 'https://example.com/page-1',
                'total_views' => 10,
                'unique_visitors' => 7,
            ],
            [
                'page_url' => 'https://example.com/page-2',
                'total_views' => 4,
                'unique_visitors' => 4,
            ],
        ]);

        $this->assertStringContainsString('https://example.com/page-1', $html);
        $this->assertStringContainsString('10', $html);
        $this->assertStringContainsString('7', $html);

        $this->assertStringContainsString('https://example.com/page-2', $html);
        $this->assertStringContainsString('4', $html);
    }
}