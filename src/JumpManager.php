<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps;

use Medas\DateTimeJumps\Appliers\TimeJump;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class JumpManager
{
    public function __construct(
        private readonly TimeJump $timeJump,
    )
    {
    }

    public function apply(Jump $jump, \DateTime $source = null): \DateTime
    {
        $dateTime = clone $source ?? new \DateTime('now', date_default_timezone_get());

        $this->timeJump->apply($jump, $dateTime);

        return $dateTime;
    }
}
