<?php

declare(strict_types=1);

namespace Medas\DateTimeJumpsTest\Functional;

use Medas\DateTimeJumps\{Jump, JumpManager, Time, Type, Weekday};
use PHPUnit\Framework\TestCase;

class JumpTest extends TestCase
{
    public function testSetTimeToday(): void
    {
        $result = $this->apply(new Jump(Type::TimeJump, new Time(8)), '2020-02-10T07:12:34');

        self::assertEquals('2020-02-10T08:00:00', $result);
    }

    public function testSetTimeTomorrow(): void
    {
        $result = $this->apply(new Jump(Type::TimeJump, new Time(8)), '2020-02-10T09:12:34');

        self::assertEquals('2020-02-11T08:00:00', $result);
    }

    public function testJumpToPartOfDay(): void
    {
        $jump = new Jump(
            Type::MultipleTimesOfDay,
            time: null,
            times: [new Time(6), new Time(12), new Time(18)]
        );

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
        $jump = new Jump(Type::NextDayOfWeek, time: new Time(8), weekday: Weekday::Friday);

        // Monday
        $result = $this->apply($jump, '2023-04-03T00:00:00');

        self::assertEquals('2023-04-07T08:00:00', $result);

        // Friday before 8:00
        $result = $this->apply($jump, '2023-04-07T03:00:00');

        self::assertEquals('2023-04-07T08:00:00', $result);

        // Friday after 08:00
        $result = $this->apply($jump, '2023-04-07T13:00:00');

        self::assertEquals('2023-04-14T08:00:00', $result);

        // Sunday
        $result = $this->apply($jump, '2023-04-09T13:00:00');

        self::assertEquals('2023-04-14T08:00:00', $result);
    }

    public function testJumpFirstWeekdayOfMonth(): void
    {
        // Jump to 8:00 on the first Monday of the month
        $jump = new Jump(Type::NthWeekdayOfMonth, time: new Time(8), weekday: Weekday::Monday);

        // Forward from Saturday
        $result = $this->apply($jump, '2023-04-01T13:00:00');

        self::assertEquals('2023-04-03T08:00:00', $result);

        // Forward on the same day
        $result = $this->apply($jump, '2023-04-03T00:00:00');

        self::assertEquals('2023-04-03T08:00:00', $result);

        // Forward to next month from the same day
        $result = $this->apply($jump, '2023-04-03T12:00:00');

        self::assertEquals('2023-05-01T08:00:00', $result);

        // Forward to next month from later in the month
        $result = $this->apply($jump, '2023-04-30T12:00:00');

        self::assertEquals('2023-05-01T08:00:00', $result);
    }

    public function testJumpLastWeekdayOfMonth(): void
    {
        // Jump to 8:00 on the last Monday of the month
        $jump = new Jump(
            Type::NthWeekdayOfMonth,
            time: new Time(8),
            weekday: Weekday::Monday,
            nthWeekdayOfMonth: -1
        );

        // Forward from Saturday
        $result = $this->apply($jump, '2023-04-01T13:00:00');

        self::assertEquals('2023-04-24T08:00:00', $result);

        // Forward on the same day
        $result = $this->apply($jump, '2023-04-24T00:00:00');

        self::assertEquals('2023-04-24T08:00:00', $result);

        // Forward to next month from the same day
        $result = $this->apply($jump, '2023-04-24T12:00:00');

        self::assertEquals('2023-05-29T08:00:00', $result);

        // Forward to next month from later in the month
        $result = $this->apply($jump, '2023-04-30T12:00:00');

        self::assertEquals('2023-05-29T08:00:00', $result);
    }

    private function apply(Jump $jump, string $dateTime): string
    {
        $string = service(JumpManager::class)
            ->apply(
                $jump,
                \DateTime::createFromFormat(\DateTimeInterface::RFC3339, $dateTime . '+00:00')
            )
            ->format(\DateTimeInterface::RFC3339);

        return substr($string, 0, -6);
    }
}
