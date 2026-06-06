<?php

declare(strict_types=1);

namespace App\Authentication;

use Tempest\Auth\Authentication\Authenticator;
use Tempest\Cryptography\Password\PasswordHasher;
use Tempest\Http\Responses\Back;
use Tempest\Http\Responses\Redirect;
use Tempest\Http\Session\Session;
use Tempest\Router\Get;
use Tempest\Router\Post as PostRoute;
use Tempest\View\View;

use function Tempest\View\view;

final readonly class AuthController
{
    public function __construct(
        private Authenticator $authenticator,
        private PasswordHasher $passwordHasher,
        private Session $session,
    ) {}

    #[Get('/login', middleware: [RedirectIfAuthenticated::class])]
    public function showLogin(): View
    {
        return view('./login.view.php', error: $this->session->get('error'));
    }

    #[PostRoute('/login', middleware: [RedirectIfAuthenticated::class])]
    public function login(LoginRequest $request): Redirect|Back
    {
        $user = User::find(email: $request->email)
            ->include('password')
            ->first();

        if ($user === null || ! $this->passwordHasher->verify($request->password, $user->password)) {
            $this->session->flash('error', 'Invalid email or password.');

            return new Back('/login');
        }

        $this->authenticator->authenticate($user);

        return new Redirect('/');
    }

    #[Get('/register', middleware: [RedirectIfAuthenticated::class])]
    public function showRegister(): View
    {
        return view('./register.view.php', error: $this->session->get('error'));
    }

    #[PostRoute('/register', middleware: [RedirectIfAuthenticated::class])]
    public function register(RegisterRequest $request): Redirect|Back
    {
        $exists = User::find(email: $request->email)->first();

        if ($exists !== null) {
            $this->session->flash('error', 'An account with that email already exists.');

            return new Back('/register');
        }

        $user = User::create(
            email: $request->email,
            password: $request->password,
        );

        $this->authenticator->authenticate($user);

        return new Redirect('/');
    }

    #[PostRoute('/logout')]
    public function logout(): Redirect
    {
        $this->authenticator->deauthenticate();

        return new Redirect('/login');
    }
}
