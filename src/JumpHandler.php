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
            if ($value->times === null) {
                throw new Exceptions\TimesIsNotSet($value);
            }

            $times = [];

            foreach ($value->times as $time) {
                $times[] = $time->hhmmss();
            }

            return json_encode([
                'type' => $value->type,
                'times' => $times,
            ]);
        }

        if ($value->time === null) {
            throw new Exceptions\TimeIsNotSet($value);
        }

        $time = $value->time->hhmmss();

        if ($value->type === Type::NthWeekdayOfMonth) {
            return json_encode([
                'type' => $value->type,
                'time' => $time,
                'weekday' => $value->weekday->value,
                'nthWeekdayOfMonth' => $value->nthWeekdayOfMonth,
            ]);
        }

        if ($value->type === Type::NthWeekdayOfQuarter) {
            return json_encode([
                'type' => $value->type,
                'time' => $time,
                'weekday' => $value->weekday->value,
                'nthWeekdayOfQuarter' => $value->nthWeekdayOfQuarter,
            ]);
        }

        if ($value->type === Type::NextDayOfWeek) {
            return json_encode([
                'type' => $value->type,
                'time' => $time,
                'weekday' => $value->weekday->value,
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

        if (!is_array($data)) {
            throw new Exceptions\CannotUnserializeValueToJump(
                $value,
                'is not a JSON encoded array'
            );
        }

        if (!array_key_exists('type', $data)) {
            throw new Exceptions\CannotUnserializeValueToJump($value, '"type" is not set');
        }

        if ($data['type'] === Type::MultipleTimesOfDay->value) {
            if (!array_key_exists('times', $data)) {
                throw new Exceptions\CannotUnserializeValueToJump($value, 'times is not set');
            }

            $times = [];

            foreach ($data['times'] as $index => $time) {
                $parts = explode(':', $time);

                if (count($parts) !== 3) {
                    throw new Exceptions\CannotUnserializeValueToJump(
                        $value,
                        "times at index $index is invalid"
                    );
                }

                $times[] = new Time((int) $parts[0], (int) $parts[1], (int) $parts[2]);
            }

            return new Jump(type: Type::MultipleTimesOfDay, times: $times);
        }

        if (!array_key_exists('time', $data)) {
            throw new Exceptions\CannotUnserializeValueToJump($value, 'time is not set');
        }

        $parts = explode(':', $data['time']);

        if (count($parts) !== 3) {
            throw new Exceptions\CannotUnserializeValueToJump($value, "time value is invalid");
        }

        $time = new Time((int) $parts[0], (int) $parts[1], (int) $parts[2]);

        if ($data['type'] === Type::NthWeekdayOfMonth->value) {
            if (!array_key_exists('weekday', $data)) {
                throw new Exceptions\CannotUnserializeValueToJump($value, 'weekday is not set');
            }

            if (!array_key_exists('nthWeekdayOfMonth', $data)) {
                throw new Exceptions\CannotUnserializeValueToJump(
                    $value,
                    'nthWeekdayOfMonth is not set'
                );
            }

            return new Jump(
                type: Type::NthWeekdayOfMonth,
                time: $time,
                weekday: Weekday::from($data['weekday']),
                nthWeekdayOfMonth: $data['nthWeekdayOfMonth'],
            );
        }

        if ($data['type'] === Type::NthWeekdayOfQuarter->value) {
            if (!array_key_exists('weekday', $data)) {
                throw new Exceptions\CannotUnserializeValueToJump($value, 'weekday is not set');
            }

            if (!array_key_exists('nthWeekdayOfQuarter', $data)) {
                throw new Exceptions\CannotUnserializeValueToJump(
                    $value,
                    'nthWeekdayOfQuarter is not set'
                );
            }

            return new Jump(
                type: Type::NthWeekdayOfQuarter,
                time: $time,
                weekday: Weekday::from($data['weekday']),
                nthWeekdayOfQuarter: $data['nthWeekdayOfQuarter'],
            );
        }

        if ($data['type'] === Type::NextDayOfWeek->value) {
            if (!array_key_exists('weekday', $data)) {
                throw new Exceptions\CannotUnserializeValueToJump($value, 'weekday is not set');
            }

            return new Jump(
                type: Type::NextDayOfWeek,
                time: $time,
                weekday: Weekday::from($data['weekday']),
            );
        }

        if ($data['type'] !== Type::TimeJump->value) {
            throw new Exceptions\CannotUnserializeValueToJump(
                $value,
                sprintf('unknown type "%s"', $data['type'])
            );
        }

        return new Jump(
            type: Type::TimeJump,
            time: $time,
        );
    }
}
