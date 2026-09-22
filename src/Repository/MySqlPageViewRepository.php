<?php

namespace App\Repository;

use PDO;

class MySqlPageViewRepository implements PageViewRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(string $pageUrl, ?string $referrer, string $visitorId, ?string $userAgent): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO page_views (page_url, referrer, visitor_id, user_agent)
             VALUES (:page_url, :referrer, :visitor_id, :user_agent)'
        );

        $stmt->execute([
            'page_url' => $pageUrl,
            'referrer' => $referrer,
            'visitor_id' => $visitorId,
            'user_agent' => $userAgent,
        ]);
    }

    public function getAggregatedStats(?\DateTimeImmutable $from = null, ?\DateTimeImmutable $to = null): array
    {
        $sql = 'SELECT
                    page_url,
                    COUNT(*) AS total_views,
                    COUNT(DISTINCT visitor_id) AS unique_visitors
                 FROM page_views';

        $params = [];

        if ($from !== null && $to !== null) {
            $sql .= ' WHERE created_at BETWEEN :from AND :to';
            $params['from'] = $from->format('Y-m-d H:i:s');
            $params['to'] = $to->format('Y-m-d H:i:s');
        }

        $sql .= ' GROUP BY page_url ORDER BY total_views DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
