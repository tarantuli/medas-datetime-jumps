<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps\Appliers;

use Medas\Core\Attributes\Service;
use Medas\DateTimeJumps\Jump;

#[Service]
class NthWeekdayOfMonth
{
    private const int DAYS_IN_WEEK = 7;

    public function apply(Jump $jump, \DateTime $source): void
    {
        $sourceTime = $source->format('H:i:s');
        $targetTime = $jump->time->hhmmss();
        $targetDay = $this->getDayInMonth($jump, $source);
        $sourceDay = (int) $source->format('j');

        $needsNextMonth = $targetDay === null
            || ($sourceDay >= $targetDay && ($sourceDay !== $targetDay || $sourceTime >= $targetTime));

        if ($needsNextMonth) {
            // Jump to that target day in the *next* month (skipping months in which the occurrence
            // does not exist, e.g., a 5th Monday in a month that only has four Mondays)
            do {
                $source->modify('next month');

                $targetDay = $this->getDayInMonth($jump, $source);
            } while ($targetDay === null);

            $sourceDay = (int) $source->format('j');
        }

        $source->modify(($targetDay - $sourceDay) . ' day')->modify($targetTime);
    }

    private function getDayInMonth(Jump $jump, \DateTime $source): int|null
    {
        $sourceClone = clone $source;
        $daysInMonth = (int) $source->format('t');

        if ($jump->nthWeekdayOfMonth >= 1) {
            $firstWeekdayInMonth
                = $sourceClone->modify('first ' . $jump->weekday->name . ' of ' . $sourceClone->format('F') . ' ' . $sourceClone->format('Y'));

            $day = (int) $firstWeekdayInMonth->format('j')
                + self::DAYS_IN_WEEK * ($jump->nthWeekdayOfMonth - 1);
        }
        else {
            $lastWeekdayInMonth
                = $sourceClone->modify('last ' . $jump->weekday->name . ' of ' . $sourceClone->format('F') . ' ' . $sourceClone->format('Y'));

            $day = (int) $lastWeekdayInMonth->format('j')
                + self::DAYS_IN_WEEK * ($jump->nthWeekdayOfMonth + 1);
        }

        // Return null when the requested occurrence does not exist in this month
        return $day >= 1 && $day <= $daysInMonth ? $day : null;
    }
}
