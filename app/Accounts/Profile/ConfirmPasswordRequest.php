<?php

declare(strict_types=1);

namespace App\Accounts\Profile;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;
use Tempest\Validation\Rules\IsNotEmptyString;

final class ConfirmPasswordRequest implements Request
{
    use IsRequest;

    #[IsNotEmptyString]
    public string $currentPassword;
}
