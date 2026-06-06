<?php

declare(strict_types=1);

namespace App\Profile;

use Tempest\Router\Get;
use Tempest\View\View;

use function Tempest\View\view;

final readonly class ProfileController
{
    #[Get('/profile')]
    public function profile(): View
    {
        return view('./profile.view.php');
    }
}
