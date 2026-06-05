# Authentication

**Source:** `vendor/tempest/framework/docs/2-features/04-authentication.md`

> **Experimental**

## Quick start

```sh
./tempest install auth
./tempest migrate:up
```

## Authenticatable model

```php app/Authentication/User.php
use Tempest\Auth\Authentication\Authenticatable;
use Tempest\Database\PrimaryKey;
use Tempest\Database\Hashed;

final class User implements Authenticatable
{
    public PrimaryKey $id;

    public function __construct(
        public string $email,
        #[Hashed, \SensitiveParameter]
        public ?string $password,
    ) {}
}
```

## Login / logout

```php app/Authentication/AuthenticationController.php
use Tempest\Auth\Authentication\Authenticator;
use Tempest\Cryptography\Password\PasswordHasher;

final readonly class AuthenticationController
{
    public function __construct(
        private Authenticator $authenticator,
        private PasswordHasher $passwordHasher,
    ) {}

    #[Post('/login')]
    public function login(LoginRequest $request): Redirect
    {
        $user = query(User::class)->select()->where('email', $request->email)->first();

        if (! $user || ! $this->passwordHasher->verify($request->password, $user->password)) {
            return new Redirect('/login')->flash('error', 'Invalid credentials');
        }

        $this->authenticator->authenticate($user);
        return new Redirect('/');
    }

    #[Post('/logout')]
    public function logout(): Redirect
    {
        $this->authenticator->deauthenticate();
        return new Redirect('/login');
    }
}
```

## Get current user

```php
// Via Authenticator
$user = $this->authenticator->current(); // returns ?Authenticatable

// Or inject the model directly (middleware must ensure authenticated)
public function __construct(private User $user) {}
public function __construct(private ?User $user) {} // nullable for unprotected routes
```

Protect route: `#[Get('/profile', middleware: [MustBeAuthenticated::class])]`

## Access control (policies)

```php app/PostPolicy.php
use Tempest\Auth\AccessControl\Policy;
use Tempest\Auth\AccessControl\AccessDecision;

final class PostPolicy
{
    #[Policy(Post::class)]
    public function create(): bool { return true; }

    #[Policy]
    public function view(Post $post): bool
    {
        return $post->published;
    }

    #[Policy(action: ['edit', 'update'])]
    public function edit(Post $post, ?User $user): bool
    {
        return $user !== null && $post->authorId === $user->id->value;
    }
}
```

Check permissions:
```php
use Tempest\Auth\AccessControl\AccessControl;

$this->accessControl->ensureGranted('delete', $post); // throws on denial
$decision = $this->accessControl->isGranted('view', $post); // returns AccessDecision

// Check without instance (for "create" actions)
$accessControl->isGranted('create', resource: Post::class, subject: $user);
```

## Custom authenticatable resolver

```php
use Tempest\Auth\Authentication\AuthenticatableResolver;

final readonly class LdapAuthenticatableResolver implements AuthenticatableResolver
{
    public function resolve(int|string $id, string $class): ?Authenticatable { ... }
    public function resolveId(Authenticatable $a): int|string { ... }
}
```

Register via initializer returning `AuthenticatableResolver`.

## OAuth

See `references/features/oauth.md`.
