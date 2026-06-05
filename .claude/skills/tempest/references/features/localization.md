# Localization

**Source:** `vendor/tempest/framework/docs/2-features/11-localization.md`

Implements MessageFormat 2.0 spec.

## Translate

```php
use Tempest\Intl\Translator;  // inject

$translator->translate('cart.expire_at', expire_at: $expiration);
$translator->translateForLocale(Locale::FRENCH, 'cart.expire_at', expire_at: $expiration);
```

Function form:
```php
use function Tempest\Intl\translate;
translate('cart.expire_at', expire_at: $expiration);
```

## Config

```php intl.config.php
use Tempest\Intl\IntlConfig;
use Tempest\Intl\Locale;

return new IntlConfig(
    currentLocale: Locale::FRENCH,
    fallbackLocale: Locale::ENGLISH,
);
```

Change locale at runtime (e.g. in middleware):
```php
$this->intlConfig->currentLocale = $user->preferredLocale;
```

## Translation files

Name format: `<name>.<locale>.{yaml,json}` — auto-discovered.

```yaml messages.en.yaml
today:
  Today is {$today :datetime pattern=|yyyy/MM/dd|}
```

Pluralization:
```yaml messages.en.yaml
cart:
  items_count:
    .input {$count :number}
    .match $count
      one   {{You have {$count} item.}}
      other {{You have {$count} items.}}
```

Runtime add:
```php
$catalog->add(Locale::FRENCH, 'order.continue_shopping', 'Continuer vos achats');
```

## Override validation messages

```yaml app/Localization/validation.en.yml
validation_error:
  is_email: |
    .input {$field :string}
    {$field} must be a valid email address.
```
