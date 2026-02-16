<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps\Appliers;

use Medas\Core\Attributes\Service;
use Medas\DateTimeJumps\Jump;

#[Service]
class NthWeekdayOfQuarter
{
    public function apply(Jump $jump, \DateTime $source): void
    {
        $target = $this->getTargetDateTimeInQuarter($jump, $source);

        // If we've already reached/passed the target moment, jump to the next quarter's target.
        if ($source->getTimestamp() >= $target->getTimestamp()) {
            $nextQuarterStart = $this->getQuarterStart($source)->modify('+3 months');
            $target = $this->getTargetDateTimeInQuarter($jump, $nextQuarterStart);
        }

        $source->setTimestamp($target->getTimestamp());
    }

    private function getTargetDateTimeInQuarter(Jump $jump, \DateTime $inQuarter): \DateTime
    {
        $targetTime = $jump->time->hhmmss();
        $quarterStart = $this->getQuarterStart($inQuarter);
        $quarterEnd = (clone $quarterStart)->modify('+3 months')->modify('-1 day');

        if ($jump->nthWeekdayOfQuarter >= 1) {
            $first = $this->firstWeekdayOnOrAfter($quarterStart, $jump->weekday->name);
            $target = $first->modify('+' . (7 * ($jump->nthWeekdayOfQuarter - 1)) . ' days');
        }
        else {
            $last = $this->lastWeekdayOnOrBefore($quarterEnd, $jump->weekday->name);
            $target = $last->modify('-' . (7 * (abs($jump->nthWeekdayOfQuarter) - 1)) . ' days');
        }

        $target->modify($targetTime);

        return $target;
    }

    private function getQuarterStart(\DateTime $source): \DateTime
    {
        // 1..12
        $month = (int) $source->format('n');

        // 1,4,7,10
        $quarterStartMonth = (int) (1 + 3 * intdiv($month - 1, 3));
        $start = clone $source;

        $start->setDate((int) $source->format('Y'), $quarterStartMonth, 1);
        $start->setTime(0, 0);

        return $start;
    }

    private function firstWeekdayOnOrAfter(\DateTime $date, string $weekdayName): \DateTime
    {
        $d = clone $date;

        if ($d->format('l') !== $weekdayName) {
            $d->modify('next ' . $weekdayName);
        }

        return $d;
    }

    private function lastWeekdayOnOrBefore(\DateTime $date, string $weekdayName): \DateTime
    {
        $d = clone $date;

        if ($d->format('l') !== $weekdayName) {
            $d->modify('last ' . $weekdayName);
        }

        return $d;
    }
}
