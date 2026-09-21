<?php

namespace Tests\Service;

use App\Service\PeriodResolver;
use PHPUnit\Framework\TestCase;

class PeriodResolverTest extends TestCase
{
    public function test_returns_start_and_end_of_current_day(): void
    {
        $resolver = new PeriodResolver();

        [$from, $to] = $resolver->resolve('today');

        $this->assertSame('00:00:00', $from->format('H:i:s'));
        $this->assertSame('23:59:59', $to->format('H:i:s'));
        $this->assertSame($from->format('Y-m-d'), $to->format('Y-m-d'));
    }

    public function test_7days_spans_seven_calendar_days(): void
    {
        $resolver = new PeriodResolver();

        [$from, $to] = $resolver->resolve('7days');

        $diffInDays = $from->diff($to)->days;

        $this->assertSame(6, $diffInDays);
    }

    public function test_30days_spans_seven_calendar_days(): void
    {
        $resolver = new PeriodResolver();

        [$from, $to] = $resolver->resolve('30days');

        $diffInDays = $from->diff($to)->days;

        $this->assertSame(29, $diffInDays);
    }

    public function test_all_returns_null_bounds(): void
    {
        $resolver = new PeriodResolver();

        [$from, $to] = $resolver->resolve('all');

        $this->assertNull($from);
        $this->assertNull($to);
    }

    public function test_unknown_period_falls_back_to_today(): void
    {
        $resolver = new PeriodResolver();

        [$from, $to] = $resolver->resolve('unknown');

        $this->assertSame('00:00:00', $from->format('H:i:s'));
        $this->assertSame('23:59:59', $to->format('H:i:s'));
        $this->assertSame($from->format('Y-m-d'), $to->format('Y-m-d'));
    }

    public function test_null_period_falls_back_to_today(): void
    {
        $resolver = new PeriodResolver();

        [$from, $to] = $resolver->resolve(null);

        $this->assertSame('00:00:00', $from->format('H:i:s'));
        $this->assertSame('23:59:59', $to->format('H:i:s'));
        $this->assertSame($from->format('Y-m-d'), $to->format('Y-m-d'));
    }
}
