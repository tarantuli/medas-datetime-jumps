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
            return json_encode(['times' => $value->times]);
        }

        if ($value->weekDayOfMonth) {
            return json_encode([
                'time' => $value->time,
                'weekDayOfMonth' => $value->weekDayOfMonth->value,
                'nthWeekDayOfMonth' => $value->nthWeekDayOfMonth,
            ]);
        }

        if ($value->weekDay) {
            return json_encode([
                'time' => $value->time,
                'weekDay' => $value->weekDay->value,
            ]);
        }

        return json_encode([
            'time' => $value->time,
        ]);
    }

    public function unserialize(mixed $value): Jump|null
    {
        if ($value === null) {
            return null;
        }

        $data = json_decode($value, true);

        if (array_key_exists('times', $data)) {
            return new Jump(times: $data['times']);
        }

        if (!array_key_exists('time', $data)) {
            throw new Exceptions\CannotUnserializeValueToJump($value, 'time is not set');
        }

        if (array_key_exists('weekDayOfMonth', $data) && array_key_exists('nthWeekDayOfMonth', $data)) {
            return new Jump(
                time: $data['time'],
                nthWeekDayOfMonth: $data['nthWeekDayOfMonth'],
                weekDayOfMonth: WeekDay::from($data['weekDayOfMonth']),
            );
        }

        if (array_key_exists('weekDay', $data)) {
            return new Jump(
                time: $data['time'],
                weekDay: WeekDay::from($data['weekDay']),
            );
        }

        return new Jump(
            time: $data['time'],
        );
    }
}
