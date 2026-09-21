<?php

namespace App\Service;

class PeriodResolver
{
    /**
     * @return array{0: ?\DateTimeImmutable, 1: ?\DateTimeImmutable}
     */
    public function resolve(?string $period): array
    {
        $now = new \DateTimeImmutable();

        return match ($period) {
            'today' => [
                $now->setTime(0, 0, 0),
                $now->setTime(23, 59, 59),
            ],
            '7days' => [
                $now->modify('-6 days')->setTime(0, 0, 0),
                $now->setTime(23, 59, 59),
            ],
            '30days' => [
                $now->modify('-29 days')->setTime(0, 0, 0),
                $now->setTime(23, 59, 59),
            ],
            'all' => [null, null],
            default => [
                $now->setTime(0, 0, 0),
                $now->setTime(23, 59, 59),
            ],
        };
    }
}
