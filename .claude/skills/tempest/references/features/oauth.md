# OAuth

**Source:** `vendor/tempest/framework/docs/2-features/17-oauth.md`

> **Experimental**

Built on PHP League's OAuth2 client.

## Quick start

```sh
./tempest install auth --oauth
# Wizard: select providers, publish config + controller stubs, add .env vars
```

## Config

```php app/Auth/github.config.php
use Tempest\Auth\OAuth\Config\GitHubOAuthConfig;
use function Tempest\env;

return new GitHubOAuthConfig(
    clientId: env('GITHUB_CLIENT_ID'),
    clientSecret: env('GITHUB_CLIENT_SECRET'),
    redirectTo: [GitHubOAuthController::class, 'callback'],
    scopes: ['user:email'],
);
```

## OAuth flow controller

```php app/Auth/DiscordOAuthController.php
use Tempest\Auth\OAuth\OAuthClient;

final readonly class DiscordOAuthController
{
    public function __construct(
        private OAuthClient $oauth,
        private Session $session,
        private Authenticator $authenticator,
    ) {}

    #[Get('/auth/discord')]
    public function redirect(): Redirect
    {
        return $this->oauth->createRedirect(scopes: ['identify']);
    }

    #[Get('/auth/discord/callback')]
    public function callback(Request $request): Redirect
    {
        $user = $this->oauth->authenticate(
            request: $request,
            map: fn (OAuthUser $user): User => query(User::class)->updateOrCreate(
                ['discord_id' => $user->id],
                ['discord_id' => $user->id, 'username' => $user->nickname, 'email' => $user->email]
            )
        );

        return new Redirect('/');
    }
}
```

## OAuthUser properties

```php
$user->id;        // provider user ID
$user->email;
$user->name;
$user->nickname;
$user->avatar;
$user->provider;
$user->raw;       // raw provider data
```

## Multiple providers

```php
return new GitHubOAuthConfig(tag: Provider::GITHUB, ...);

// Inject with tag:
#[Tag(Provider::GITHUB)] private OAuthClient $githubClient
```

## Available providers

`GitHubOAuthConfig`, `GoogleOAuthConfig`, `FacebookOAuthConfig`, `DiscordOAuthConfig`, `InstagramOAuthConfig`, `LinkedInOAuthConfig`, `MicrosoftOAuthConfig`, `SlackOAuthConfig`, `AppleOAuthConfig`, `TwitchOAuthConfig`, `GenericOAuthConfig`.

## Testing

```php
$oauth = $this->oauth->fake(new OAuthUser(
    id: 'jon',
    email: 'jon@test.com',
    nickname: 'jondoe',
));

$this->http->get('/oauth/discord')->assertRedirect($oauth->lastAuthorizationUrl);
$oauth->assertAuthorizationUrlGenerated();

$this->http->get('/oauth/discord/callback', query: [
    'code' => 'fake-code',
    'state' => $oauth->getState()
])->assertRedirect('/');

$oauth->assertUserFetched(code: 'fake-code');
```
