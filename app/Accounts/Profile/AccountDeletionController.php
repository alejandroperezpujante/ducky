<?php

declare(strict_types=1);

namespace App\Accounts\Profile;

use App\Accounts\Authentication\MustBeAuthenticated;
use App\Accounts\User;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\Cryptography\Password\PasswordHasher;
use Tempest\DateTime\Duration;
use Tempest\Http\Request;
use Tempest\Http\Responses\Back;
use Tempest\Http\Responses\Redirect;
use Tempest\Http\Session\Session;
use Tempest\Router\Get;
use Tempest\Router\Post as PostRoute;
use Tempest\Router\UriGenerator;
use Tempest\View\View;

use function Tempest\Container\get;
use function Tempest\Router\temporary_signed_uri;
use function Tempest\Router\uri;
use function Tempest\View\view;

final readonly class AccountDeletionController
{
    public function __construct(
        private Authenticator $authenticator,
        private PasswordHasher $passwordHasher,
        private Session $session,
        private UriGenerator $uriGenerator,
    ) {}

    /**
     * Step 1: User confirms password → receive email with signed deletion link.
     */
    #[PostRoute('/profile/delete/request', middleware: [MustBeAuthenticated::class])]
    public function requestDeletion(ConfirmPasswordRequest $request): Redirect|Back
    {
        /** @var User $user */
        $user = $this->authenticator->current();
        $userWithPassword = User::find(id: $user->id->value)->include('password')->first();

        if (! $this->passwordHasher->verify($request->currentPassword, $userWithPassword->password)) {
            $this->session->flash('error', 'Incorrect password. Please try again.');

            return new Back(uri([ProfileController::class, 'show']));
        }

        $signedUrl = temporary_signed_uri(
            action: [self::class, 'showConfirm'],
            duration: Duration::hours(24),
        );

        // TODO: send email with $signedUrl to $user->email
        // Mail should warn: clicking confirm schedules deletion in 30 days.

        $this->session->flash('success', 'A deletion confirmation link has been sent to ' . $user->email . '. It expires in 24 hours.');

        return new Redirect(uri([ProfileController::class, 'show']));
    }

    /**
     * Step 2: User clicks signed link → final confirmation form (password required).
     */
    #[Get('/profile/delete/confirm', middleware: [MustBeAuthenticated::class])]
    public function showConfirm(): View|Redirect
    {
        /** @var Request $rawRequest */
        $rawRequest = get(Request::class);

        if (! $this->uriGenerator->hasValidSignature($rawRequest)) {
            $this->session->flash('error', 'This link is invalid or has expired.');

            return new Redirect(uri([ProfileController::class, 'show']));
        }

        return view(
            './account-delete-confirm.view.php',
            formAction: $rawRequest->uri,
            error: $this->session->get('error'),
        );
    }

    /**
     * Step 3: User confirms with password → schedule account deletion in 30 days.
     */
    #[PostRoute('/profile/delete/confirm', middleware: [MustBeAuthenticated::class])]
    public function confirm(ConfirmPasswordRequest $request): Redirect|Back
    {
        /** @var Request $rawRequest */
        $rawRequest = get(Request::class);

        if (! $this->uriGenerator->hasValidSignature($rawRequest)) {
            $this->session->flash('error', 'This link is invalid or has expired.');

            return new Redirect(uri([ProfileController::class, 'show']));
        }

        /** @var User $user */
        $user = $this->authenticator->current();
        $userWithPassword = User::find(id: $user->id->value)->include('password')->first();

        if (! $this->passwordHasher->verify($request->currentPassword, $userWithPassword->password)) {
            $this->session->flash('error', 'Incorrect password.');

            return new Back($rawRequest->uri);
        }

        // TODO: set $user->scheduledDeletionAt = DateTime::now()->plus(Duration::days(30)) on the User model
        // TODO: add a scheduled console command (#[Schedule(Every::DAY)]) to delete users past their scheduledDeletionAt
        // TODO: send a confirmation email informing the user their account will be deleted in 30 days

        $this->session->flash('success', 'Your account has been scheduled for deletion in 30 days. You will receive a confirmation email.');

        return new Redirect(uri([ProfileController::class, 'show']));
    }
}
