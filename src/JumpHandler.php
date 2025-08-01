<?php

declare(strict_types=1);

namespace Medas\DateTimeJumps;

use Medas\Core\{
    Attributes\Service,
    Interfaces\PropertyHandler,
    Interfaces\Type as HandledType,
    Types\Text
};

#[Service]
readonly class JumpHandler implements PropertyHandler
{
    public function type(): HandledType
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

        if ($value->type === Type::MultipleTimesOfDay) {
            $times = [];

            foreach ($value->times as $time) {
                $times[] = $time->hhmmss();
            }

            return json_encode([
                'type' => $value->type,
                'times' => $times,
            ]);
        }

        $time = $value->time->hhmmss();

        if ($value->type === Type::NthWeekdayOfMonth) {
            return json_encode([
                'type' => $value->type,
                'time' => $time,
                'weekDayOfMonth' => $value->weekDay->value,
                'nthWeekDayOfMonth' => $value->nthWeekDayOfMonth,
            ]);
        }

        if ($value->type === Type::NextDayOfWeek) {
            return json_encode([
                'type' => $value->type,
                'time' => $time,
                'weekDay' => $value->weekDay->value,
            ]);
        }

        return json_encode([
            'type' => $value->type,
            'time' => $time,
        ]);
    }

    public function unserialize(mixed $value): Jump|null
    {
        if ($value === null) {
            return null;
        }

        $data = json_decode($value, true);

        if ($data['type'] === Type::MultipleTimesOfDay) {
            if (!array_key_exists('times', $data)) {
                throw new Exceptions\CannotUnserializeValueToJump($value, 'times is not set');
            }

            $times = [];

            foreach ($data['times'] as $time) {
                $parts = explode(':', $time);
                $times[] = new Time((int) $parts[0], (int) $parts[1], (int) $parts[2]);
            }

            return new Jump(type: Type::MultipleTimesOfDay, times: $times);
        }

        if (!array_key_exists('time', $data)) {
            throw new Exceptions\CannotUnserializeValueToJump($value, 'time is not set');
        }

        $parts = explode(':', $data['time']);
        $time = new Time((int) $parts[0], (int) $parts[1], (int) $parts[2]);

        if ($data['type'] === Type::NthWeekdayOfMonth) {
            if (!array_key_exists('weekDay', $data)) {
                throw new Exceptions\CannotUnserializeValueToJump($value, 'weekDay is not set');
            }

            if (!array_key_exists('nthWeekDayOfMonth', $data)) {
                throw new Exceptions\CannotUnserializeValueToJump(
                    $value,
                    'nthWeekDayOfMonth is not set'
                );
            }

            return new Jump(
                type: Type::NthWeekdayOfMonth,
                time: $time,
                weekDay: WeekDay::from($data['weekDay']),
                nthWeekDayOfMonth: $data['nthWeekDayOfMonth'],
            );
        }

        if ($data['type'] === Type::NextDayOfWeek) {
            if (!array_key_exists('weekDay', $data)) {
                throw new Exceptions\CannotUnserializeValueToJump($value, 'weekDay is not set');
            }

            return new Jump(
                type: Type::NextDayOfWeek,
                time: $time,
                weekDay: WeekDay::from($data['weekDay']),
            );
        }

        return new Jump(
            type: Type::TimeJump,
            time: $time,
        );
    }
}
