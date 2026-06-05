# Mail

**Source:** `vendor/tempest/framework/docs/2-features/07-mail.md`

## Quick start (SMTP)

```env .env
MAIL_SMTP_HOST=mail.provider.com
MAIL_SMTP_PORT=587
MAIL_SMTP_USERNAME=user@provider.com
MAIL_SMTP_PASSWORD=secret
MAIL_SENDER_NAME=Brent
MAIL_SENDER_EMAIL=brendt@stitcher.io
```

## Send mail

```php
use Tempest\Mail\Mailer;
use Tempest\Mail\GenericEmail;

$this->mailer->send(new GenericEmail(
    subject: 'Welcome!',
    to: $user->email,
    html: view(__DIR__ . '/mails/welcome.view.php', user: $user),
));
```

## Custom email class (preferred)

```php app/WelcomeEmail.php
use Tempest\Mail\Email;
use Tempest\Mail\Envelope;
use Tempest\View\View;
use function Tempest\View\view;

final class WelcomeEmail implements Email
{
    public function __construct(private readonly User $user) {}

    public Envelope $envelope {
        get => new Envelope(
            subject: 'Welcome',
            to: $this->user->email,
        );
    }

    public string|View $html {
        get => view('welcome.view.php', user: $this->user);
    }
}
```

Inject `Mailer` and call `$this->mailer->send(new WelcomeEmail($user))`.

## Text-only content

```php
use Tempest\Mail\HasTextContent;

final class WelcomeEmail implements Email, HasTextContent
{
    public string|View|null $text {
        get => view('welcome-text.view.php', user: $this->user);
    }
}
```

## Attachments

```php
use Tempest\Mail\HasAttachments;
use Tempest\Mail\Attachment;

final class WelcomeEmail implements Email, HasAttachments
{
    public array $attachments {
        get => [
            Attachment::fromFilesystem(__DIR__ . '/welcome.pdf'),
            Attachment::fromStorage($s3Storage, '/file.pdf'),
        ];
    }
}
```

## Other transports

```php app/mail.config.php
use Tempest\Mail\Transports\Postmark\PostmarkConfig;
use function Tempest\env;

return new PostmarkConfig(key: env('MAIL_POSTMARK_TOKEN'));
```

Built-in: SMTP (default), `PostmarkConfig`, Amazon SES via Symfony driver.

## Events

```php
use Tempest\EventBus\EventHandler;
use Tempest\Mail\EmailWasSent;
use Tempest\Mail\EmailSendingFailed;

#[EventHandler]
public function onSent(EmailWasSent $event): void { ... }

#[EventHandler]
public function onFailed(EmailSendingFailed $event): void {
    // $event->email, $event->exception
}
```

## Testing

```php
$this->mailer->send(new WelcomeEmail($user))
    ->assertSentTo($user->email)
    ->assertAttached('welcome.pdf');

$this->mailer->shouldFail();
try { $this->mailer->send(...); } catch (TransportException) {}
$this->mailer->assertFailed(WelcomeEmail::class);
```

Mails in tests are never actually sent.
