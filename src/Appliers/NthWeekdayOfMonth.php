<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps\Appliers;

use Medas\Core\Attributes\Service;
use Medas\DateTimeJumps\Jump;

#[Service]
class NthWeekdayOfMonth
{
    public function apply(Jump $jump, \DateTime $source): void
    {
        $sourceTime = $source->format('H:i:s');
        $targetTime = $jump->time->hhmmss();

        $targetDay = $this->getDayInSourceMonth($jump, $source);
        $sourceDay = (int) $source->format('j');

        if ($sourceDay >= $targetDay && ($sourceDay !== $targetDay || $sourceTime >= $targetTime)) {
            // Jump to that target day in the *next* month
            $source->modify('next month');

            $targetDay = $this->getDayInSourceMonth($jump, $source);
            $sourceDay = (int) $source->format('j');
        }

        $source->modify(($targetDay - $sourceDay) . ' day')->modify($targetTime);
    }

    private function getDayInSourceMonth(Jump $jump, \DateTime $source): int
    {
        if ($jump->nthWeekDayOfMonth >= 1) {
            $firstWeekdayInMonth = (clone $source)->modify('first ' . $jump->weekDayOfMonth->name . ' of ' . $source->format('F') . ' ' . $source->format('Y'));
            return (int) $firstWeekdayInMonth->format('j') + 7 * ($jump->nthWeekDayOfMonth - 1);
        }
        else {
            $lastWeekdayInMonth = (clone $source)->modify('last ' . $jump->weekDayOfMonth->name . ' of ' . $source->format('F') . ' ' . $source->format('Y'));
            return (int) $lastWeekdayInMonth->format('j') + 7 * ($jump->nthWeekDayOfMonth + 1);
        }
    }
}
