<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps;

use Medas\Core\{Attributes\Service, Interfaces\PropertyHandler, Interfaces\Type, Types\Text};

#[Service]
readonly class JumpHandler implements PropertyHandler
{
    public function type(): Type
    {
        return new Text();
    }

    public function serialize(mixed $value): string|null
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof Jump) {
            throw new Exceptions\ValueMustBeInstanceOfJump($value);
        }

        if ($value->times) {
            $times = [];

            foreach ($value->times as $time) {
                $times[] = $time->hhmmss();
            }

            return json_encode(['times' => $times]);
        }

        $time = $value->time->hhmmss();

        if ($value->weekDayOfMonth) {
            return json_encode([
                'time' => $time,
                'weekDayOfMonth' => $value->weekDayOfMonth->value,
                'nthWeekDayOfMonth' => $value->nthWeekDayOfMonth,
            ]);
        }

        if ($value->weekDay) {
            return json_encode([
                'time' => $time,
                'weekDay' => $value->weekDay->value,
            ]);
        }

        return json_encode([
            'time' => $time,
        ]);
    }

    public function unserialize(mixed $value): Jump|null
    {
        if ($value === null) {
            return null;
        }

        $data = json_decode($value, true);

        if (array_key_exists('times', $data)) {
            $times = [];

            foreach ($data['times'] as $time) {
                $parts = explode(':', $time);
                $times[] = new Time((int) $parts[0], (int) $parts[1], (int) $parts[2]);
            }

            return new Jump(times: $times);
        }

        if (!array_key_exists('time', $data)) {
            throw new Exceptions\CannotUnserializeValueToJump($value, 'time is not set');
        }

        $parts = explode(':', $data['time']);
        $time = new Time((int) $parts[0], (int) $parts[1], (int) $parts[2]);

        if (array_key_exists('weekDayOfMonth', $data) && array_key_exists('nthWeekDayOfMonth', $data)) {
            return new Jump(
                time: $time,
                nthWeekDayOfMonth: $data['nthWeekDayOfMonth'],
                weekDayOfMonth: WeekDay::from($data['weekDayOfMonth']),
            );
        }

        if (array_key_exists('weekDay', $data)) {
            return new Jump(
                time: $time,
                weekDay: WeekDay::from($data['weekDay']),
            );
        }

        return new Jump(
            time: $time,
        );
    }
}
