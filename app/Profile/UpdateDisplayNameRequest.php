<?php

declare(strict_types=1);

namespace App\Profile;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;
use Tempest\Validation\SkipValidation;

final class UpdateDisplayNameRequest implements Request
{
    use IsRequest;

    /** Display name may be empty (clears it, falling back to username) */
    #[SkipValidation]
    public string $displayName = '';
}
