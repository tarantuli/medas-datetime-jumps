<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps\Appliers;

use Medas\Core\Attributes\Service;
use Medas\DateTimeJumps\Jump;

#[Service]
class TimeJump
{
    public function apply(Jump $jump, \DateTime $source): void
    {
        $targetTime = $jump->time->hhmmss();

        if ($this->timestamp($source) >= $jump->time->timestamp()) {
            $source->modify('+1 day ' . $targetTime);
        }
        else {
            $source->modify($targetTime);
        }
    }

    private function timestamp(\DateTime $date): int
    {
        return (3600 * (int) $date->format('H'))
            + 60 * (int) $date->format('i')
            + (int) $date->format('s');
    }
}
