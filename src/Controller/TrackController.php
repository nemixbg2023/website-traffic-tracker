<?php

namespace App\Controller;

use App\Repository\PageViewRepositoryInterface;
use App\Service\TrackingService;

class TrackController

{
    private PageViewRepositoryInterface $repository;
    private TrackingService $service;

    public function __construct(PageViewRepositoryInterface $repository, TrackingService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function handle(string $rawBody): void
    {
        // --- 1. Resolve visitor ID (existing cookie or new one) ---
        $existingVisitorId = $_COOKIE['ttrk_visitor_id'] ?? null;
        $visitorId = $this->service->resolveVisitorId($existingVisitorId);

        if ($existingVisitorId === null) {
            setcookie('ttrk_visitor_id', $visitorId, [
                'expires' => time() + (60 * 60 * 24 * 365), // 1 year
                'path' => '/',
                'samesite' => 'None',
                'secure' => true,
            ]);
        }

        // --- 2. Read requested data ---
        $data = json_decode($rawBody, true);

        $pageUrl = $data['page_url'] ?? null;
        $referrer = $data['referrer'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        if ($pageUrl === null || filter_var($pageUrl, FILTER_VALIDATE_URL) === false) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required field: Page URL']);
            return;
        }

        $pageUrl = $this->service->normalizePageUrl($pageUrl);

        // --- 3. Save via repository ---
        $this->repository->save($pageUrl, $referrer, $visitorId, $userAgent);

        http_response_code(204);
    }
}