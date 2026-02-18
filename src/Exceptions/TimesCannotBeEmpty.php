<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps\Exceptions;

use Medas\Core\Exceptions\BaseException;

class TimesCannotBeEmpty extends BaseException
{
    public function pattern(): string
    {
        return 'Times is set, but empty';
    }
}
