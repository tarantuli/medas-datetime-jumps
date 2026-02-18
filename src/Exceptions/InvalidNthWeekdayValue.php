<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps\Exceptions;

use Medas\Core\Exceptions\BaseException;

class InvalidNthWeekdayValue extends BaseException
{
    public function __construct(int $amount)
    {
        parent::__construct($amount);
    }

    public function pattern(): string
    {
        return 'invalid nth weekday parameter passed: %s';
    }
}
