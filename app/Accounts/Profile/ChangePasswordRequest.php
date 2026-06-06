<?php

declare(strict_types=1);

namespace App\Accounts\Profile;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;
use Tempest\Validation\Rules\IsNotEmptyString;
use Tempest\Validation\Rules\IsPassword;

final class ChangePasswordRequest implements Request
{
    use IsRequest;

    #[IsNotEmptyString]
    public string $currentPassword;

    #[IsPassword(min: 8)]
    public string $newPassword;

    #[IsNotEmptyString]
    public string $newPasswordConfirmation;
}
