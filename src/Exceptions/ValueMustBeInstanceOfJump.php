<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps\Exceptions;

use Medas\Core\Exceptions\BaseException;

class ValueMustBeInstanceOfJump extends BaseException
{
    public function __construct(mixed $value)
    {
        parent::__construct(get_debug_type($value));
    }

    public function pattern(): string
    {
        return 'value must be instance of Jump, %s given';
    }
}
