<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps;

enum Type: string
{
    case MultipleTimesOfDay = 'multiple-times-of-day';
    case NextDayOfWeek = 'next-day-of-week';
    case NthWeekdayOfMonth = 'nth-weekday-of-month';
    case NthWeekdayOfQuarter = 'nth-weekday-of-quarter';
    case TimeJump = 'time-jump';
}
