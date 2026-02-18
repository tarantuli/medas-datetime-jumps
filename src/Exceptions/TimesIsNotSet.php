<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps\Exceptions;

use Medas\Core\Exceptions\BaseException;
use Medas\DateTimeJumps\Jump;

class TimesIsNotSet extends BaseException
{
    public function __construct(Jump $jump)
    {
        parent::__construct(serialize($jump));
    }

    public function pattern(): string
    {
        return 'Times is not set on jump %s';
    }
}
