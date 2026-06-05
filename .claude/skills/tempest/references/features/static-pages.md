# Static Pages

**Source:** `vendor/tempest/framework/docs/2-features/13-static-pages.md`

## Mark a route as static

```php app/FrontPageController.php
use Tempest\Router\Get;
use Tempest\Router\StaticPage;
use Tempest\View\View;
use function Tempest\View\view;

final readonly class FrontPageController
{
    #[StaticPage]
    #[Get('/')]
    public function frontpage(): View
    {
        return view('./front-page');
    }
}
```

## Generate / clean

```sh
./tempest static:generate              # compile all static pages to public/
./tempest static:generate --crawl      # + check for dead internal links
./tempest static:generate --crawl --external  # + check external links
./tempest static:clean                 # remove all HTML files from public/
```

Static pages go to `public/<path>/index.html`. Most web servers serve these automatically.

## Data providers (dynamic pages)

```php app/Documentation/ChapterController.php
#[StaticPage(ChapterDataProvider::class)]
#[Get('/{category}/{slug}')]
public function show(string $category, string $slug, ChapterRepository $chapters): View { ... }
```

```php app/Documentation/ChapterDataProvider.php
use Tempest\Router\DataProvider;

final readonly class ChapterDataProvider implements DataProvider
{
    public function __construct(private ChapterRepository $chapters) {}

    public function provide(): Generator
    {
        foreach ($this->chapters->all() as $chapter) {
            yield ['category' => $chapter->category, 'slug' => $chapter->slug];
        }
    }
}
```

Each `yield` generates one page.

## Production

Add to deploy script:
```sh
./tempest static:clean
./tempest static:generate
```
