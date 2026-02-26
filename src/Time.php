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
        if ($this->hour < 0 || $this->hour > 23) {
            throw new Exceptions\InvalidHourPassed($this->hour);
        }

        if ($this->minute < 0 || $this->minute > 59) {
            throw new Exceptions\InvalidMinutePassed($this->minute);
        }

        if ($this->second < 0 || $this->second > 59) {
            throw new Exceptions\InvalidSecondPassed($this->second);
        }
    }

    public function hhmmss(): string
    {
        return sprintf('%02u:%02u:%02u', $this->hour, $this->minute, $this->second);
    }

    public function timestamp(): int
    {
        return $this->hour * 3600 + $this->minute * 60 + $this->second;
    }
}
