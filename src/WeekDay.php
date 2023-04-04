<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps;

/**
 * Equal to date format specifier "N": ISO 8601 numeric representation of the day of the week
 */
enum WeekDay: int
{
    case Monday = 1;
    case Tuesday = 2;
    case Wednesday = 3;
    case Thursday = 4;
    case Friday = 5;
    case Saturday = 6;
    case Sunday = 7;
}
