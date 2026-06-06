<?php

declare(strict_types=1);

namespace App\Accounts\Profile;

use App\Accounts\Authentication\MustBeAuthenticated;
use App\Accounts\Authentication\SessionInvalidator;
use App\Accounts\User;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\Cryptography\Password\PasswordHasher;
use Tempest\DateTime\Duration;
use Tempest\Http\Request;
use Tempest\Http\Responses\Back;
use Tempest\Http\Responses\Redirect;
use Tempest\Http\Session\Session;
use Tempest\Mail\GenericEmail;
use Tempest\Mail\Mailer;
use Tempest\Router\Get;
use Tempest\Router\Post as PostRoute;
use Tempest\Router\UriGenerator;
use Tempest\View\View;

use function Tempest\Container\get;
use function Tempest\Router\temporary_signed_uri;
use function Tempest\Router\uri;
use function Tempest\View\view;

final readonly class PasswordChangeController
{
    public function __construct(
        private Authenticator $authenticator,
        private PasswordHasher $passwordHasher,
        private Session $session,
        private Mailer $mailer,
        private UriGenerator $uriGenerator,
        private SessionInvalidator $sessionInvalidator,
    ) {}

    /**
     * Step 1: User confirms current password → receive email with signed link.
     */
    #[PostRoute('/profile/password/request', middleware: [MustBeAuthenticated::class])]
    public function requestChange(ConfirmPasswordRequest $request): Redirect|Back
    {
        /** @var User $user */
        $user = $this->authenticator->current();
        $userWithPassword = User::find(id: $user->id->value)->include('password')->first();

        if (! $this->passwordHasher->verify($request->currentPassword, $userWithPassword->password)) {
            $this->session->flash('error', 'Incorrect password. Please try again.');

            return new Back(uri([ProfileController::class, 'show']));
        }

        $signedUrl = temporary_signed_uri(
            action: [self::class, 'showForm'],
            duration: Duration::hours(1),
        );

        $this->mailer->send(new GenericEmail(
            subject: 'Change your password',
            to: $user->email,
            html: view('./emails/password-change.view.php', url: $signedUrl, user: $user),
        ));

        $this->session->flash('success', 'A confirmation link has been sent to ' . $user->email . '. It expires in 1 hour.');

        return new Redirect(uri([ProfileController::class, 'show']));
    }

    /**
     * Step 2: User clicks the signed link → password-protected form to enter new password.
     */
    #[Get('/profile/password/change', middleware: [MustBeAuthenticated::class])]
    public function showForm(): View|Redirect
    {
        /** @var Request $rawRequest */
        $rawRequest = get(Request::class);

        if (! $this->uriGenerator->hasValidSignature($rawRequest)) {
            $this->session->flash('error', 'This link is invalid or has expired. Please request a new one.');

            return new Redirect(uri([ProfileController::class, 'show']));
        }

        return view(
            './password-change-form.view.php',
            formAction: $rawRequest->uri,
            error: $this->session->get('error'),
        );
    }

    /**
     * Step 3: User submits new password + confirmation → update and invalidate sessions.
     */
    #[PostRoute('/profile/password/change', middleware: [MustBeAuthenticated::class])]
    public function submit(ChangePasswordRequest $request): Redirect|Back
    {
        /** @var Request $rawRequest */
        $rawRequest = get(Request::class);

        if (! $this->uriGenerator->hasValidSignature($rawRequest)) {
            $this->session->flash('error', 'This link is invalid or has expired. Please request a new one.');

            return new Redirect(uri([ProfileController::class, 'show']));
        }

        /** @var User $user */
        $user = $this->authenticator->current();
        $userWithPassword = User::find(id: $user->id->value)->include('password')->first();

        if (! $this->passwordHasher->verify($request->currentPassword, $userWithPassword->password)) {
            $this->session->flash('error', 'Incorrect current password.');

            return new Back($rawRequest->uri);
        }

        if ($request->newPassword !== $request->newPasswordConfirmation) {
            $this->session->flash('error', 'New passwords do not match.');

            return new Back($rawRequest->uri);
        }

        // #[Hashed] on User::$password means assigning plaintext + calling update() hashes automatically.
        $user->update(password: $request->newPassword);

        $this->sessionInvalidator->invalidateAllFor($user);
        $this->authenticator->deauthenticate();

        $this->session->flash('success', 'Your password has been updated. Please sign in again.');

        return new Redirect('/login');
    }
}
