<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps;

use Medas\Core\Attributes\Service;

#[Service]
readonly class JumpManager
{
    public function __construct(
        private Appliers\NextDayOfWeek     $nextDayOfWeek,
        private Appliers\NthWeekdayOfMonth $nthWeekdayOfMonth,
        private Appliers\TimeJump          $timeJump,
        private Appliers\TimesOfDay        $timesOfDay,
    )
    {
    }

    public function apply(Jump $jump, \DateTime $source = null): \DateTime
    {
        $dateTime = $source
            ? clone $source
            : new \DateTime('now', new \DateTimeZone(date_default_timezone_get()));

        // First check for multiple times
        if ($jump->type === Type::MultipleTimesOfDay) {
            $this->timesOfDay->apply($jump, $dateTime);

            return $dateTime;
        }

        // All other jumps require time to be set
        if (!$jump->time) {
            throw new Exceptions\TimeIsNotSet($jump);
        }

        if ($jump->type === Type::NthWeekdayOfMonth) {
            $this->nthWeekdayOfMonth->apply($jump, $dateTime);

            return $dateTime;
        }

        if ($jump->type === Type::NextDayOfWeek) {
            $this->nextDayOfWeek->apply($jump, $dateTime);

            return $dateTime;
        }

        if ($jump->type === Type::TimeJump) {
            $this->timeJump->apply($jump, $dateTime);

            return $dateTime;
        }

        throw new Exceptions\JumpTypeNotImplemented($jump->type);
    }
}
