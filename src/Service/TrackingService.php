<?php

namespace App\Service;

class TrackingService
{
    public function resolveVisitorId(?string $existingVisitorId): string
    {
        // If visitor already has a visitor ID (from a previus visit's cookie), reuse it.
        // Otherwise, this is a new visitor - generate a fresh, random one.
        if ($existingVisitorId !== null) {
            return $existingVisitorId;
        }

        return bin2hex(random_bytes(16));
    }
}