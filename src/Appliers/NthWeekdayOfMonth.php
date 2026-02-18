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
        $targetDay = $this->getDayInMonth($jump, $source);
        $sourceDay = (int) $source->format('j');

        if ($sourceDay >= $targetDay && ($sourceDay !== $targetDay || $sourceTime >= $targetTime)) {
            // Jump to that target day in the *next* month
            $source->modify('next month');

            $targetDay = $this->getDayInMonth($jump, $source);
            $sourceDay = (int) $source->format('j');
        }

        $source->modify(($targetDay - $sourceDay) . ' day')->modify($targetTime);
    }

    private function getDayInMonth(Jump $jump, \DateTime $source): int
    {
        $sourceClone = clone $source;

        if ($jump->nthWeekdayOfMonth >= 1) {
            $firstWeekdayInMonth
                = $sourceClone->modify('first ' . $jump->weekday->name . ' of ' . $sourceClone->format('F') . ' ' . $sourceClone->format('Y'));

            return (int) $firstWeekdayInMonth->format('j') + 7 * ($jump->nthWeekdayOfMonth - 1);
        }
        else {
            $lastWeekdayInMonth
                = $sourceClone->modify('last ' . $jump->weekday->name . ' of ' . $sourceClone->format('F') . ' ' . $sourceClone->format('Y'));

            return (int) $lastWeekdayInMonth->format('j') + 7 * ($jump->nthWeekdayOfMonth + 1);
        }
    }
}
