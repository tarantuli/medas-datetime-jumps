<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps\Appliers;

use Medas\Core\Attributes\Service;
use Medas\DateTimeJumps\{Exceptions\TimesIsNotSet, Jump};

#[Service]
class TimesOfDay
{
    public function apply(Jump $jump, \DateTime $source): void
    {
        if (!array_key_exists(0, $jump->times)) {
            throw new TimesIsNotSet($jump);
        }

        $sourceTime = $source->format('H:i:s');

        foreach ($jump->times as $time) {
            $targetTime = $time->hhmmss();

            if ($targetTime > $sourceTime) {
                // Jump to this time
                $source->modify($targetTime);

                return;
            }
        }

        // Jump to the first time, next day
        $source->modify('+1 day ' . $jump->times[0]->hhmmss());
    }
}
