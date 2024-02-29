<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps;

class Jump
{
    public function __construct(
        public readonly Time|null    $time,

        /** @var Time[] */
        public readonly array|null   $times = null,
        public readonly WeekDay|null $weekDay = null,

        /** @var WeekDay[] */
        public readonly array|null   $weekDays = null,

        /**
         *  1 = first week day of the month  \
         *  2 = second...  \
         * -1 = last week day of month  \
         * -2 = second to last...
         */
        public readonly int          $nthWeekDayOfMonth = 1,
        public readonly WeekDay|null $weekDayOfMonth = null,
    )
    {
    }
}
