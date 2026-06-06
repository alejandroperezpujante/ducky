<?php

declare(strict_types=1);

namespace App\Profile;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;
use Tempest\Validation\Rules\IsNotEmptyString;

final class UpdateUsernameRequest implements Request
{
    use IsRequest;

    #[IsNotEmptyString]
    public string $username;
}
