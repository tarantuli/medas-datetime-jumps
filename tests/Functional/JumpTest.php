<?php

declare(strict_types=1);

namespace Medas\DateTimeJumpsTest\Functional;

use DateTimeInterface;
use Medas\DateTimeJumps\Jump;
use Medas\DateTimeJumps\JumpManager;
use Medas\DateTimeJumps\Time;
use PHPUnit\Framework\TestCase;

class JumpTest extends TestCase
{
    public function testSetTimeToday(): void
    {
        $result = $this->apply(new Jump(new Time(8)), '2020-02-10T07:12:34');
        self::assertEquals('2020-02-10T08:00:00', $result);
    }

    private function apply(Jump $jump, string $dateTime): string
    {
        $string = service(JumpManager::class)
            ->apply($jump, \DateTime::createFromFormat(DateTimeInterface::RFC3339, $dateTime . '+00:00'))
            ->format(DateTimeInterface::RFC3339);

        return substr($string, 0, -6);
    }

    public function testSetTimeTomorrow(): void
    {
        $result = $this->apply(new Jump(new Time(8)), '2020-02-10T09:12:34');
        self::assertEquals('2020-02-11T08:00:00', $result);
    }
}
