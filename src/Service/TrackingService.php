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

    public function normalizePageUrl(string $pageUrl): string
    {
        $parts = parse_url($pageUrl);
        // If parse_url couldn't extract a scheme or host, the URL is malformed—
        // return it as-is and let upstream validation (filter_var) reject it
        if (!isset($parts['scheme'], $parts['host'])) {
            return $pageUrl;
        }

        $normalized = $parts['scheme'] . '://' . $parts['host'];

        if (isset($parts['path'])) {
            $normalized .= $parts['path'];
        }
        
        return $normalized;
    }
}