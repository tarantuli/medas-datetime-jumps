<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps\Exceptions;

use Medas\Core\Exceptions\BaseException;

class InvalidWeekdayParameterPassed extends BaseException
{
    public function __construct(int $index, mixed $value)
    {
        parent::__construct($index, get_debug_type($value));
    }

    public function pattern(): string
    {
        return 'invalid weekday passed at index %s: %s';
    }
}
