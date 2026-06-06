<?php

declare(strict_types=1);

namespace App\Accounts\Preferences;

use Tempest\Router\Get;
use Tempest\View\View;

use function Tempest\View\view;

final readonly class PreferencesController
{
    #[Get('/preferences')]
    public function preferences(): View
    {
        return view('./preferences.view.php');
    }
}
