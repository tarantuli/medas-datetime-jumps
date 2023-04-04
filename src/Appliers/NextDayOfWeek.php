<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps\Appliers;

use Medas\DateTimeJumps\Jump;
use Medas\DateTimeJumps\WeekDay;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class NextDayOfWeek
{
    public function apply(Jump $jump, \DateTime $source): void
    {
        $sourceWeekday = WeekDay::from((int) $source->format('N'));
        $sourceTime = $source->format('H:i:s');

        $targetWeekday = $jump->weekDay;
        $targetTime = $jump->time->hhmmss();

        if ($sourceWeekday === $targetWeekday) {
            if ($sourceTime < $targetTime) {
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
