<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps;

use Medas\DateTimeJumps\Appliers\TimeJump;
use Medas\DateTimeJumps\Appliers\TimesOfDay;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class JumpManager
{
    public function __construct(
        private readonly TimeJump   $timeJump,
        private readonly TimesOfDay $timesOfDay,
    )
    {
    }

    public function apply(Jump $jump, \DateTime $source = null): \DateTime
    {
        $dateTime = $source ? clone $source : new \DateTime('now', date_default_timezone_get());

        if ($jump->times) {
            $this->timesOfDay->apply($jump, $dateTime);
            return $dateTime;
        }

        if (!$jump->time) {
            throw new Exceptions\TimeIsNotSet($jump);
        }

        $this->timeJump->apply($jump, $dateTime);

        return $dateTime;
    }
}
