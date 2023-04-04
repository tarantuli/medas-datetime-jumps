<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps;

use Medas\DateTimeJumps\Appliers\{NextDayOfWeek, NthWeekdayOfMonth, TimeJump, TimesOfDay};
use Medas\ServiceManager\Attributes\Service;

#[Service]
class JumpManager
{
    public function __construct(
        private readonly NextDayOfWeek     $nextDayOfWeek,
        private readonly NthWeekdayOfMonth $nthWeekdayOfMonth,
        private readonly TimeJump          $timeJump,
        private readonly TimesOfDay        $timesOfDay,
    )
    {
    }

    public function apply(Jump $jump, \DateTime $source = null): \DateTime
    {
        $dateTime = $source ? clone $source : new \DateTime('now', date_default_timezone_get());

        // First check for multiple times
        if ($jump->times) {
            $this->timesOfDay->apply($jump, $dateTime);
            return $dateTime;
        }

        // All other jumps require time to be set
        if (!$jump->time) {
            throw new Exceptions\TimeIsNotSet($jump);
        }

        if ($jump->weekDayOfMonth) {
            $this->nthWeekdayOfMonth->apply($jump, $dateTime);
            return $dateTime;
        }

        if ($jump->weekDay) {
            $this->nextDayOfWeek->apply($jump, $dateTime);
            return $dateTime;
        }

        $this->timeJump->apply($jump, $dateTime);

        return $dateTime;
    }
}
