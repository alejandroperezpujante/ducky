<?php

declare(strict_types=1);

namespace App;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;
use Tempest\Validation\Rules\HasLength;

final class StorePostRequest implements Request
{
    use IsRequest;

    #[HasLength(min: 1)]
    public string $content;
}
