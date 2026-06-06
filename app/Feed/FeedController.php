<?php

declare(strict_types=1);

namespace App\Feed;

use Tempest\Router\Get;
use Tempest\View\View;

use function Tempest\View\view;

final readonly class FeedController
{
    #[Get('/feed')]
    public function feed(): View
    {
        return view('./feed.view.php');
    }
}
