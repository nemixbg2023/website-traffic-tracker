<?php

namespace App\View;

class DashboardView
{
    public function render(array $stats, string $selectedPeriod = 'today'): string
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

            <form method="get" action="/dashboard">
                <label for="period">Period:</label>
                <select name="period" id="period" onchange="this.form.submit()">
                    <option value="today" <?= $selectedPeriod === 'today' ? 'selected' : '' ?>>Today</option>
                    <option value="7days" <?= $selectedPeriod === '7days' ? 'selected' : '' ?>>Last 7 days</option>
                    <option value="30days" <?= $selectedPeriod === '30days' ? 'selected' : '' ?>>Last 30 days</option>
                    <option value="all" <?= $selectedPeriod === 'all' ? 'selected' : '' ?>>All time</option>
                </select>
            </form>

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
