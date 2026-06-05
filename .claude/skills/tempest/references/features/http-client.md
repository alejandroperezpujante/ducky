# HTTP Client

**Source:** `vendor/tempest/framework/docs/2-features/12-http-client.md`

Doc is stub/hidden. Tempest doesn't ship a first-party HTTP client wrapper.

Use Symfony's HttpClient directly:

```sh
composer require symfony/http-client
```

```php
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class GitHubService
{
    public function __construct(private HttpClientInterface $http) {}

    public function getUser(string $username): array
    {
        $response = $this->http->request('GET', "https://api.github.com/users/{$username}");
        return $response->toArray();
    }
}
```

Register via initializer if you need custom config (base URL, auth headers, etc.).
