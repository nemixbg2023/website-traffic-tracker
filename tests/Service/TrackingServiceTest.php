<?php

namespace Tests\Service;

use App\Service\TrackingService;
use PHPUnit\Framework\TestCase;

class TrackingServiceTest extends TestCase
{
    public function test_returns_existing_visitor_id_when_provided(): void
    {
        $service = new TrackingService();
        $existingVisitor = 'existing-id-123';

        $result = $service->resolveVisitorId($existingVisitor);

        $this->assertSame($existingVisitor, $result);
    }

    public function test_generates_new_visitor_when_none_provided(): void
    {
        $service = new TrackingService();

        $result = $service->resolveVisitorId(null);

        // A freshly generated ID should be a 32-character hex string
        // (16 random bytes, each represented by 2 hex characters)
        $this->assertIsString($result);
        $this->assertSame(32, strlen($result));
    }

    public function test_generates_defferent_ids_on_each_call(): void
    {
        $service = new TrackingService();

        $firstCall = $service->resolveVisitorId(null);
        $secondCall = $service->resolveVisitorId(null);

        $this->assertNotSame($firstCall, $secondCall);
    }

    public function test_normalize_page_url_strips_query_string(): void
    {
        $service = new TrackingService();

        $result = $service->normalizePageUrl('https://example.com/page?utm_source=facebook&session=abc123');

        $this->assertSame('https://example.com/page', $result);
    }

    public function test_normalize_page_url_keeps_url_without_query_string_unchanged(): void
    {
        $service = new TrackingService();

        $result = $service->normalizePageUrl('https://example.com/page');

        $this->assertSame('https://example.com/page', $result);
    }
}