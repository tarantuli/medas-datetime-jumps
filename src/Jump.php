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
        public Weekday|null $weekday = null,

        /** @var Weekday[] */
        public array|null   $weekdays = null,

        /**
         *  1 = first week day of the month  \
         *  2 = second...  \
         * -1 = last week day of the month  \
         * -2 = second to last...
         */
        public int          $nthWeekdayOfMonth = 1,

        /**
         *  1 = first week day of the quarter  \
         *  2 = second...  \
         * -1 = last week day of the quarter \
         * -2 = second to last...
         */
        public int          $nthWeekdayOfQuarter = 1,
    )
    {
    }
}
