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

        if ($sourceWeekday === $targetWeekday) {
            if ($this->timestamp($source) < $jump->time->timestamp()) {
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

    private function timestamp(\DateTime $date): int
    {
        return (3600 * (int) $date->format('H'))
            + 60 * (int) $date->format('i')
            + (int) $date->format('s');
    }
}
