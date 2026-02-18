<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps\Exceptions;

use Medas\Core\Exceptions\BaseException;

class InvalidMinutesPassed extends BaseException
{
    public function __construct(int $amount)
    {
        parent::__construct($amount);
    }

    public function pattern(): string
    {
        return 'invalid amount of minutes passed: %s';
    }
}
