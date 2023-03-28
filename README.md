# medas-datetime-jumps

Part of the [Medas framework](https://github.com/tarantuli/medas-core).

Applies time interval jump definitions to dates.

## Time jump
A jump with only a time definition will move the given time forward to the next instance of that time, either on the given date or on the next date.

````php
$jump = new Jump(time: new Time(8, 0, 0));

$date = \DateTime::createFromFormat(\DateTimeInterface::RFC3339, '2020-02-10T07:12:34+00:00');
$newDate = service(JumpManager::class)->apply($jump, $date);
// $newDate = '2020-02-10T08:00:00+00:00'

$date = \DateTime::createFromFormat(\DateTimeInterface::RFC3339, '2020-02-10T09:12:34+00:00');
$newDate = service(JumpManager::class)->apply($jump, $date);
// $newDate = '2020-02-11T08:00:00+00:00'
````
