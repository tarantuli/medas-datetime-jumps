<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps\Appliers;

use Medas\DateTimeJumps\Jump;
use Medas\ServiceManager\Service;

#[Service]
class TimeJump
{
    public function apply(Jump $jump, \DateTime $source): void
    {
        $targetTime = $jump->time->hhmmss();

        if ($source->format('H:i:s') >= $targetTime) {
            $source->modify('+1 day ' . $targetTime);
        }
        else {
            $source->modify($targetTime);
        }
    }
}
