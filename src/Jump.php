<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps;

class Jump
{
    public function __construct(
        public readonly Time|null  $time,
        /** @var Time[] */
        public readonly array|null $times = null,
    )
    {
    }
}
