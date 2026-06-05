# Date and Time

**Source:** `vendor/tempest/framework/docs/2-features/15-datetime.md`

Tempest's own `DateTime` — not Carbon, not PHP's native DateTime. Optional: use any other.

## Create

```php
use Tempest\DateTime\DateTime;

DateTime::parse('2025-09-19 02:00:00');
DateTime::parse($timestamp);
DateTime::fromPattern('2025-09-19 02:00', pattern: 'yyyy-MM-dd HH:mm');  // ICU format
DateTime::now();
```

Or inject `Clock`:
```php
use Tempest\Clock\Clock;

final readonly class HomeController
{
    public function __construct(private readonly Clock $clock) {}
    public function __invoke(): View
    {
        return view('./home.view.php', now: $this->clock->now());
    }
}
```

## Manipulate

```php
$date->plus(Duration::seconds(30));
$date->plusHour();
$date->plusMinutes(30);
$date->minusDay();
$date->endOfDay();
```

Timezone:
```php
use Tempest\DateTime\Timezone;
$date->convertToTimezone(Timezone::EUROPE_PARIS);
```

Duration between dates:
```php
$duration = $date1->between($date2);  // returns Duration
```

## Compare

```php
$date->isBefore($other);
$date->isBeforeOrAt($other);
$date->isAfter($other);
$date->isFuture();
$date->betweenTimeInclusive($d1, $d2);
```

## Format

```php
use Tempest\DateTime\FormatPattern;
use Tempest\Intl\Locale;

$date->format();                                         // Jan 7, 2026, 10:30:05 PM
$date->format(pattern: FormatPattern::COOKIE);           // Wed, 07-Jan-2026 22:30:46 UTC
$date->format(locale: Locale::FRENCH);                   // 7 janv. 2026, 22:32:12
```

## Duration

```php
use Tempest\DateTime\Duration;

Duration::seconds(30);
Duration::minutes(5);
Duration::hours(12);
Duration::days(30);
```

## Testing time

```php
$clock = $this->clock();
$clock->setNow('2025-09-19 02:00:00');
$clock->sleep(milliseconds: 250);
// DateTime::now() and Tempest\now() use the test clock
```

## PSR-20

```php
$psrClock = $clock->toPsrClock();
```
