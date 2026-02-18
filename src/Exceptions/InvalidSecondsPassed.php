<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps\Exceptions;

use Medas\Core\Exceptions\BaseException;

class InvalidSecondsPassed extends BaseException
{
    public function __construct(int $amount)
    {
        parent::__construct($amount);
    }

    public function pattern(): string
    {
        return 'invalid amount of seconds passed: %s';
    }
}
