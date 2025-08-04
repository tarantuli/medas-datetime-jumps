<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps;

readonly class Time
{
    public function __construct(
        public int $hour,
        public int $minute = 0,
        public int $second = 0,
    )
    {
    }

    public function hhmmss(): string
    {
        return sprintf('%02u:%02u:%02u', $this->hour, $this->minute, $this->second);
    }
}
