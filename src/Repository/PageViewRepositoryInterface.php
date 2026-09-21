<?php

namespace App\Repository;

interface PageViewRepositoryInterface
{
    public function save(string $pageUrl, ?string $referrer, string $visitorId, ?string $userAgent): void;

    public function getAggregatedStats(): array;
}
