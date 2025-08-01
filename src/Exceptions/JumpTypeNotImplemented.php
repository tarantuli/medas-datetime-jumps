<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps\Exceptions;

use Medas\Core\Exceptions\BaseException;
use Medas\DateTimeJumps\Type;

class JumpTypeNotImplemented extends BaseException
{
    public function __construct(Type $type)
    {
        parent::__construct($type->name);
    }

    public function pattern(): string
    {
        return 'Jump type %s has not been implemented';
    }
}
