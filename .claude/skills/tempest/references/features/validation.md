# Validation

**Source:** `vendor/tempest/framework/docs/2-features/03-validation.md`

## Automatic validation (request objects)

Request classes validated automatically on injection — see Routing reference.

## Manual validation with Validator

```php
use Tempest\Validation\Validator;  // inject from container

// Against class properties
$failures = $this->validator->validateValuesForClass(Book::class, [
    'title' => 'Timeline Taxi',
    'publishedAt' => '2024-10-01',
]);

// Against specific rules
$failures = $this->validator->validateValues([
    'email' => 'jon@doe.co',
    'age' => 25,
], [
    'email' => [new IsEmail()],
    'age' => [new IsInteger(), new IsNotNull()],
]);

// Single value
$failures = $this->validator->validateValue('jon@doe.co', [new IsEmail()]);
$failures = $this->validator->validateValue('jon@doe.co', fn ($v) => str_contains($v, '@'));
```

## Validation attributes on models

```php
use Tempest\Validation\Rules;
use Tempest\Validation\SkipValidation;

final class Book
{
    #[Rules\HasLength(min: 5, max: 50)]
    public string $title;

    #[Rules\IsNotEmptyString]
    public string $description;

    #[Rules\HasDateTimeFormat('Y-m-d')]
    public ?DateTime $publishedAt = null;

    #[SkipValidation]  // never validated
    public string $internalNote;
}
```

All rules: `vendor/tempest/framework/packages/validation/src/Rules/` or [GitHub](https://github.com/tempestphp/tempest-framework/tree/main/packages/validation/src/Rules).

## Error messages

```php
$errors = Arr\map_iterable(
    $failures,
    fn (FailingRule $f) => $this->validator->getErrorMessage($f)
);

$this->validator->getErrorMessage($failure, 'email');
// => 'Email must be a valid email address'
```

## Override translation messages

```yaml app/Localization/validation.en.yml
validation_error:
  is_email: |
    .input {$field :string}
    {$field} must be a valid email address.
```

Per-property key override:
```php
#[Rules\HasLength(min: 5, max: 50)]
#[TranslationKey('book_management.book_title')]
public string $title;
```
