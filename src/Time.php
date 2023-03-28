<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps;

class Time
{
    public function __construct(
        public readonly int $hour,
        public readonly int $minutes = 0,
        public readonly int $seconds = 0,
    )
    {
    }

    public function hhmmss(): string
    {
        return sprintf('%02u:%02u:%02u', $this->hour, $this->minutes, $this->seconds);
    }
}
