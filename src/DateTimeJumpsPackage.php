<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps;

use Medas\ServiceManager\{AsSingleton, BasePackage};

class DateTimeJumpsPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return $this->dependenciesByClass([
        ]);
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
