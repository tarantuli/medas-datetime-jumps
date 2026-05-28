# medas-datetime-jumps

Part of the [Medas framework](https://github.com/tarantuli/medas-core).

## Description

Advances a `\DateTime` to the next occurrence of a recurring time pattern. Given a source moment and a `Jump` definition, `JumpManager::apply()` returns a new `\DateTime` set to the next instance of that pattern strictly after (or at) the source — it never stays at the source if it already satisfies the pattern.

`Jump` is an immutable value object combining a `Type` enum with supporting `Time`, `Weekday`, and nth-position fields. Five types are supported:

| `Type`                | Required fields                          | Description                                                   |
|-----------------------|------------------------------------------|---------------------------------------------------------------|
| `TimeJump`            | `time`                                   | Next occurrence of a specific time of day                     |
| `MultipleTimesOfDay`  | `times`                                  | Next occurrence of any time in an ordered list                |
| `NextDayOfWeek`       | `time`, `weekday`                        | Next occurrence of a specific weekday at a specific time      |
| `NthWeekdayOfMonth`   | `time`, `weekday`, `nthWeekdayOfMonth`   | Nth (or −Nth from end) weekday of the current or next month   |
| `NthWeekdayOfQuarter` | `time`, `weekday`, `nthWeekdayOfQuarter` | Nth (or −Nth from end) weekday of the current or next quarter |

`Time` validates hour (0–23), minute (0–59), and second (0–59) on construction. `Weekday` is an ISO 8601 int-backed enum (Monday = 1 … Sunday = 7). For nth-weekday types, positive values count from the start (1 = first) and negative values count from the end (−1 = last).

`JumpHandler` implements `PropertyHandler` and serialises/deserialises `Jump` objects to and from a JSON string, making them storable in any text-typed ORM property.

## Usage

### Package developer context

Register the package and inject `JumpManager` wherever jumps need to be applied:

```php
use Medas\DateTimeJumps\DateTimeJumpsPackage;

DateTimeJumpsPackage::instance();
```

**`Type::TimeJump` — next occurrence of a time of day:**

```php
use Medas\DateTimeJumps\{Jump, JumpManager, Time, Type};

$jump = new Jump(type: Type::TimeJump, time: new Time(8, 0, 0));

// Source is before 08:00 → same day
$source = new \DateTime('2026-05-21T07:30:00');
$result = $jumpManager->apply($jump, $source);
// 2026-05-21T08:00:00

// Source is after 08:00 → next day
$source = new \DateTime('2026-05-21T09:00:00');
$result = $jumpManager->apply($jump, $source);
// 2026-05-22T08:00:00
```

**`Type::MultipleTimesOfDay` — next occurrence among several times:**

```php
use Medas\DateTimeJumps\{Jump, Time, Type};

$jump = new Jump(
    type: Type::MultipleTimesOfDay,
    times: [new Time(8, 0), new Time(12, 0), new Time(16, 0)],
);

// Source is 11:00 → jumps to 12:00 same day
$result = $jumpManager->apply($jump, new \DateTime('2026-05-21T11:00:00'));
// 2026-05-21T12:00:00

// Source is 17:00 → wraps to 08:00 next day
$result = $jumpManager->apply($jump, new \DateTime('2026-05-21T17:00:00'));
// 2026-05-22T08:00:00
```

**`Type::NextDayOfWeek` — next occurrence of a weekday at a time:**

```php
use Medas\DateTimeJumps\{Jump, Time, Type, Weekday};

$jump = new Jump(
    type: Type::NextDayOfWeek,
    time: new Time(9, 0),
    weekday: Weekday::Monday,
);

// Source is Wednesday → next Monday
$result = $jumpManager->apply($jump, new \DateTime('2026-05-20T10:00:00')); // Wednesday
// 2026-05-25T09:00:00 (Monday)

// Source is Monday before 09:00 → same Monday
$result = $jumpManager->apply($jump, new \DateTime('2026-05-25T08:00:00'));
// 2026-05-25T09:00:00

// Source is Monday after 09:00 → next Monday (7 days ahead)
$result = $jumpManager->apply($jump, new \DateTime('2026-05-25T10:00:00'));
// 2026-06-01T09:00:00
```

**`Type::NthWeekdayOfMonth` — nth weekday of the month:**

```php
use Medas\DateTimeJumps\{Jump, Time, Type, Weekday};

// First Monday of the month at 09:00
$jump = new Jump(
    type: Type::NthWeekdayOfMonth,
    time: new Time(9, 0),
    weekday: Weekday::Monday,
    nthWeekdayOfMonth: 1,
);

// Last Friday of the month at 17:00
$jump = new Jump(
    type: Type::NthWeekdayOfMonth,
    time: new Time(17, 0),
    weekday: Weekday::Friday,
    nthWeekdayOfMonth: -1,
);

$result = $jumpManager->apply($jump, new \DateTime('2026-05-21T10:00:00'));
// Jumps to the last Friday of May 2026 at 17:00, or June if already past it
```

Months when the requested occurrence does not exist (e.g., a 5th Monday in a short month) are skipped automatically.

**`Type::NthWeekdayOfQuarter` — nth weekday of the quarter:**

```php
use Medas\DateTimeJumps\{Jump, Time, Type, Weekday};

// Second Tuesday of the quarter at 10:00
$jump = new Jump(
    type: Type::NthWeekdayOfQuarter,
    time: new Time(10, 0),
    weekday: Weekday::Tuesday,
    nthWeekdayOfQuarter: 2,
);

// Last Wednesday of the quarter at 15:30
$jump = new Jump(
    type: Type::NthWeekdayOfQuarter,
    time: new Time(15, 30),
    weekday: Weekday::Wednesday,
    nthWeekdayOfQuarter: -1,
);

$result = $jumpManager->apply($jump, new \DateTime('2026-05-21T10:00:00'));
// Jumps to the target occurrence in Q2 2026, or Q3 if already past it
```

**Applying a jump from now:**

```php
// Omitting the second argument uses the current time
$next = $jumpManager->apply($jump);
```

**Serialising a `Jump` for storage:**

```php
use Medas\DateTimeJumps\JumpHandler;

$handler = service(JumpHandler::class);

// To a JSON string (e.g., for an ORM text column)
$serialized = $handler->serialize($jump);
// '{"type":"nth-weekday-of-month","time":"09:00:00","weekday":1,"nthWeekdayOfMonth":1}'

// Back to a Jump object
$jump = $handler->unserialize($serialized);
```

### Backend user context

Jumps are typically stored alongside a scheduler entity (e.g., a recurring task or notification rule) and evaluated at runtime to compute the next trigger time:

```php
// Load the jump definition from storage
$jump = $jumpHandler->unserialize($task->jumpDefinition);

// Find the next trigger moment after now
$nextRun = $jumpManager->apply($jump);

// Schedule the task
$task->nextRunAt = $nextRun;
```

**`Time` field summary:**

```php
new Time(hour: 8)              // 08:00:00
new Time(hour: 8, minute: 30)  // 08:30:00
new Time(8, 30, 45)            // 08:30:45
```

**`nthWeekdayOfMonth` / `nthWeekdayOfQuarter` values:**

| Value | Meaning                   |
|-------|---------------------------|
| `1`   | First occurrence          |
| `2`   | Second occurrence         |
| `-1`  | Last occurrence           |
| `-2`  | Second-to-last occurrence |
