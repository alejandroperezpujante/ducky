<?php

declare(strict_types=1);

namespace App\Messages;

use Tempest\Router\Get;
use Tempest\View\View;

use function Tempest\View\view;

final readonly class MessagesController
{
    #[Get('/messages')]
    public function messages(): View
    {
        return view('./messages.view.php');
    }
}
