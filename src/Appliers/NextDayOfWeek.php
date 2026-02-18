<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps\Appliers;

use Medas\Core\Attributes\Service;
use Medas\DateTimeJumps\{Jump, Weekday};

#[Service]
class NextDayOfWeek
{
    public function apply(Jump $jump, \DateTime $source): void
    {
        $sourceWeekday = Weekday::from((int) $source->format('N'));
        $targetWeekday = $jump->weekday;
        $targetTime = $jump->time->hhmmss();
        $targetTimestamp = $jump->time->timestamp();

        if ($sourceWeekday === $targetWeekday) {
            if ($source->getTimestamp() < $targetTimestamp) {
                $source->modify($targetTime);
            }
            else {
                $source->modify('+7 days ' . $targetTime);
            }
        }
        else {
            $source->modify($targetWeekday->name . ' ' . $targetTime);
        }
    }
}
