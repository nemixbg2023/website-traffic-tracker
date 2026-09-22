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

    public function test_marks_correct_option_as_selected_for_each_period(): void
    {
        $view = new DashboardView();

        $periods = ['today', '7days', '30days', 'all'];

        foreach ($periods as $period) {
            $html = $view->render([], $period);

            $this->assertStringContainsString(
                "value=\"{$period}\" selected",
                $html,
                "Expected '{$period}' option to be marked as selected"
            );
        }
    }

    public function test_dafaults_to_today_selected_when_no_period_given(): void
    {
        $view = new DashboardView();

        $html = $view->render([]);

        $this->assertStringContainsString('value="today" selected', $html);
    }

    public function test_only_one_option_is_selected_at_a_time(): void
    {
        $view = new DashboardView();

        $html = $view->render([], '7days');

        $this->assertStringContainsString('value="7days" selected', $html);
    }
}
