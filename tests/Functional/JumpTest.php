<?php

declare(strict_types=1);

namespace Medas\DateTimeJumpsTest\Functional;

use DateTimeInterface;
use Medas\DateTimeJumps\Jump;
use Medas\DateTimeJumps\JumpManager;
use Medas\DateTimeJumps\Time;
use Medas\DateTimeJumps\WeekDay;
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

    public function testJumpToPartOfDay(): void
    {
        $jump = new Jump(time: null, times: [new Time(6), new Time(12), new Time(18)]);

        // 00:00
        $result = $this->apply($jump, '2020-02-10T00:00:00');
        self::assertEquals('2020-02-10T06:00:00', $result);

        // 06:00
        $result = $this->apply($jump, '2020-02-10T06:00:00');
        self::assertEquals('2020-02-10T12:00:00', $result);

        // 10:00
        $result = $this->apply($jump, '2020-02-10T10:00:00');
        self::assertEquals('2020-02-10T12:00:00', $result);

        // 14:00
        $result = $this->apply($jump, '2020-02-10T14:00:00');
        self::assertEquals('2020-02-10T18:00:00', $result);

        // 17:59:59
        $result = $this->apply($jump, '2020-02-10T17:59:59');
        self::assertEquals('2020-02-10T18:00:00', $result);

        // 20:00
        $result = $this->apply($jump, '2020-02-10T20:00:00');
        self::assertEquals('2020-02-11T06:00:00', $result);
    }

    public function testJumpNextDayOfWeek(): void
    {
        // Jump to 8:00 on Friday
        $jump = new Jump(time: new Time(8), weekDay: WeekDay::Friday);

        // Monday
        $result = $this->apply($jump, '2023-04-03T00:00:00');
        self::assertEquals('2023-04-07T08:00:00', $result);

        // Friday morning
        $result = $this->apply($jump, '2023-04-07T03:00:00');
        self::assertEquals('2023-04-07T08:00:00', $result);

        // Friday afternoon
        $result = $this->apply($jump, '2023-04-07T13:00:00');
        self::assertEquals('2023-04-14T08:00:00', $result);

        // Sunday
        $result = $this->apply($jump, '2023-04-09T13:00:00');
        self::assertEquals('2023-04-14T08:00:00', $result);
    }
}
