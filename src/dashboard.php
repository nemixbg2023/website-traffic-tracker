<?php

// --- 1. Conect to database ---

$pdo = new PDO(
    'mysql:host=db;dbname=traffic_tracker;charset=utf8mb4',
    'tracker_user',
    'trackerpass'
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// --- 2. Query aggregated stats per page ---

// GROUP_BY page_url: one row per page
// COUNT(*): total requests (every page load, including repeat visits from the same person)
// COUNT(DISTINCT visior_id): unique visits – counts each visitor only once per page,
// regardless of how many times they reloaded it
$stmt = $pdo->query(
    'SELECT
        page_url,
        COUNT(*) AS total_views,
        COUNT(DISTINCT visitor_id) AS unique_visitors
    FROM page_views
    GROUP BY page_url
    ORDER BY total_views DESC'
);
$stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Traffic Tracker Dashboard</title>
    </head>
    <body>
        <h1>Traffic Tracker Dashboard</h1>

        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>Page URL</th>
                    <th>Total Views</th>
                    <th>Unique Visitors</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stats as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['page_url']) ?></td>
                        <td><?= $row['total_views'] ?></td>
                        <td><?= $row['unique_visitors'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </body>
</html>