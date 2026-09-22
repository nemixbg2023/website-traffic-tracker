<?php

namespace Tests\Repository;

use PDO;
use App\Repository\MySqlPageViewRepository;
use Override;
use PHPUnit\Framework\TestCase;

class MySqlPageViewRepositoryTest extends TestCase
{
    private PDO $pdo;
    private MySqlPageViewRepository $repository;

    protected function setUp(): void
    {
        $this->pdo = new PDO(
            'mysql:host=db;dbname=traffic_tracker_test;charset=utf8mb4',
            'tracker_user',
            'trackerpass'
        );

        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->repository = new MySqlPageViewRepository($this->pdo);
    }

    protected function tearDown(): void
    {
        // Clean the table after each test so tests don't affect one another
        $this->pdo->exec('TRUNCATE TABLE page_views');
    }

    public function test_save_inserts_a_page_view(): void
    {
        $this->repository->save(
            'https://examples.com/page-1',
            'https://google.com',
            'visitor-abc',
            'TestAgent/1.0'
        );

        $stmt = $this->pdo->query('SELECT * FROM page_views');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->assertCount(1, $rows);
        $this->assertSame('https://examples.com/page-1', $rows[0]['page_url']);
        $this->assertSame('visitor-abc', $rows[0]['visitor_id']);
    }

    public function test_get_aggregated_stats_counts_total_and_unique_visitors(): void
    {
        // Same visitor, same page, twice—should count as 2 total views, 1 unique visitor
        $this->repository->save('https://examples.com/page-1', null, 'visitor_1', null);
        $this->repository->save('https://examples.com/page-1', null, 'visitor_1', null);

        // Different visitor, same page—should add to total views AND unique visitors
        $this->repository->save('https://examples.com/page-1', null, 'visitor_2', null);

        $stats = $this->repository->getAggregatedStats();

        $this->assertCount(1, $stats);
        $this->assertSame('https://examples.com/page-1', $stats[0]['page_url']);
        $this->assertSame(3, (int) $stats[0]['total_views']);
        $this->assertSame(2, (int) $stats[0]['unique_visitors']);
    }

    public function test_get_aggregated_stats_filters_by_date_range(): void
    {
        // Insert one view "today" (using the repository's own save method,
        // which always sets created_at to NOW via the database default)
        $this->repository->save('https://example.com/page-1', null, 'visitor-today', null);

        // Manually insert a page view with an OLD created_at, bypassing save()
        // since save() always uses CURRENT_TIMESTAMP
        $this->pdo->exec(
            "INSERT INTO page_views (page_url, visitor_id, created_at)
             VALUES ('https://example.com/page-1', 'visitor-old', '2020-01-01 00:00:00')"
        );

        // Filter for "today" only - should exclude the old 2020 entry
        $from = new \DateTimeImmutable('today');
        $to = new \DateTimeImmutable('today 23:59:59');

        $stats = $this->repository->getAggregatedStats($from, $to);

        $this->assertCount(1, $stats);
        $this->assertSame(1, (int) $stats[0]['total_views']);
    }
}
