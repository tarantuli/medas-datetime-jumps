<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps\Appliers;

use Medas\Core\Attributes\Service;
use Medas\DateTimeJumps\Jump;

#[Service]
class NthWeekdayOfQuarter
{
    private const int DAYS_IN_WEEK = 7;
    private const int MONTHS_IN_QUARTER = 3;

    public function apply(Jump $jump, \DateTime $source): void
    {
        $target = $this->getTargetDateTimeInQuarter($jump, $source);

        // If we've already reached/passed the target moment, jump to the next quarter's target.
        if ($source->getTimestamp() >= $target->getTimestamp()) {
            $nextQuarterStart = $this->getQuarterStart($source)->modify(sprintf("+%d months", self::MONTHS_IN_QUARTER));
            $target = $this->getTargetDateTimeInQuarter($jump, $nextQuarterStart);
        }

        $source->setTimestamp($target->getTimestamp());
    }

    private function getTargetDateTimeInQuarter(Jump $jump, \DateTime $inQuarter): \DateTime
    {
        $targetTime = $jump->time->hhmmss();
        $quarterStart = $this->getQuarterStart($inQuarter);
        $quarterEnd = (clone $quarterStart)->modify(sprintf("+%d months", self::MONTHS_IN_QUARTER))->modify('-1 day');

        if ($jump->nthWeekdayOfQuarter >= 1) {
            $first = $this->firstWeekdayOnOrAfter($quarterStart, $jump->weekday->name);
            $target = $first->modify('+' . (self::DAYS_IN_WEEK * ($jump->nthWeekdayOfQuarter - 1)) . ' days');
        }
        else {
            $last = $this->lastWeekdayOnOrBefore($quarterEnd, $jump->weekday->name);
            $target = $last->modify('-' . (self::DAYS_IN_WEEK * (abs($jump->nthWeekdayOfQuarter) - 1)) . ' days');
        }

        $target->modify($targetTime);

        return $target;
    }

    private function getQuarterStart(\DateTime $source): \DateTime
    {
        // 1..12
        $month = (int) $source->format('n');

        // 1,4,7,10
        $quarterStartMonth = (int) (1 + self::MONTHS_IN_QUARTER * intdiv($month - 1, self::MONTHS_IN_QUARTER));
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
