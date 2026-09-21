<?php

namespace App\View;

class DashboardView
{
    public function render(array $stats): string
    {
        ob_start();
?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <title>Traffic TrackerDashboard</title>
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
<?php
        return ob_get_clean();
    }
}
