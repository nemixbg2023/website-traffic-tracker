<?php

namespace App\Controller;

use App\Repository\PageViewRepositoryInterface;
use App\View\DashboardView;

class DashboardController
{
    private PageViewRepositoryInterface $repository;
    private DashboardView $view;

    public function __construct(PageViewRepositoryInterface $repository, DashboardView $view)
    {
        $this->repository = $repository;
        $this->view = $view;
    }

    public function handle(): void
    {
        $stats = $this->repository->getAggregatedStats();

        echo $this->view->render($stats);
    }
}
