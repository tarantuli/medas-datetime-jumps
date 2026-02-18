<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps\Exceptions;

use Medas\Core\Exceptions\BaseException;
use Medas\DateTimeJumps\Jump;

class TimeIsNotSet extends BaseException
{
    public function __construct(Jump $jump)
    {
        parent::__construct(json_encode($jump));
    }

    public function pattern(): string
    {
        return 'Time is not set on jump %s';
    }
}
