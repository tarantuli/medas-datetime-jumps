<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps\Exceptions;

use Medas\Core\Exceptions\BaseException;

class CannotUnserializeValueToJump extends BaseException
{
    public function __construct(string $value, string $reason)
    {
        parent::__construct($value, $reason);
    }

    public function pattern(): string
    {
        return 'cannot unserialize value "%s" to Jump: %s';
    }
}
