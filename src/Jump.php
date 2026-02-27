<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps;

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
        if ($this->times !== null) {
            if (count($this->times) === 0) {
                throw new Exceptions\TimesCannotBeEmpty();
            }

            foreach ($this->times as $i => $time) {
                if (!$time instanceof Time) {
                    throw new Exceptions\InvalidTimeParameterPassed(
                        $i,
                        $time,
                    );
                }
            }
        }

        if ($this->weekdays !== null) {
            foreach ($this->weekdays as $i => $weekday) {
                if (!$weekday instanceof Weekday) {
                    throw new Exceptions\InvalidWeekdayParameterPassed(
                        $i,
                        $weekday,
                    );
                }
            }
        }

        if ($this->nthWeekdayOfMonth === 0 || abs($this->nthWeekdayOfMonth) > 5) {
            throw new Exceptions\InvalidNthWeekdayValue($this->nthWeekdayOfMonth);
        }

        if ($this->nthWeekdayOfQuarter === 0 || abs($this->nthWeekdayOfQuarter) > 15) {
            throw new Exceptions\InvalidNthWeekdayValue($this->nthWeekdayOfQuarter);
        }
    }
}
