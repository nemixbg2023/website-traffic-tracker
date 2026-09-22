<?php

namespace App\Controller;

use App\Repository\PageViewRepositoryInterface;
use App\Service\PeriodResolver;
use App\View\DashboardView;

class DashboardController
{
    private PageViewRepositoryInterface $repository;
    private DashboardView $view;
    private PeriodResolver $periodResolver;

    public function __construct(
        PageViewRepositoryInterface $repository,
        DashboardView $view,
        PeriodResolver $periodResolver
    ) {
        $this->repository = $repository;
        $this->view = $view;
        $this->periodResolver = $periodResolver;
    }

    public function handle(): void
    {
        $period = $_GET['period'] ?? 'today';

        [$from, $to] = $this->periodResolver->resolve($period);

        $stats = $this->repository->getAggregatedStats($from, $to);

        echo $this->view->render($stats, $period);
    }
}
