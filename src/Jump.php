<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps;

use Medas\Core\Attributes\DumpObject;

#[DumpObject]
readonly class Jump
{
    public function __construct(
        public Type         $type,
        public Time|null    $time = null,

        /** @var Time[] */
        public array|null   $times = null,
        public WeekDay|null $weekDay = null,

        /** @var WeekDay[] */
        public array|null   $weekDays = null,

        /**
         *  1 = first week day of the month  \
         *  2 = second...  \
         * -1 = last week day of the month  \
         * -2 = second to last...
         */
        public int          $nthWeekDayOfMonth = 1,
    )
    {
    }
}
