<?php

declare(strict_types=1);

namespace App\Authentication;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;
use Tempest\Validation\Rules\IsEmail;
use Tempest\Validation\Rules\IsNotEmptyString;
use Tempest\Validation\Rules\IsPassword;

final class RegisterRequest implements Request
{
    use IsRequest;

    #[IsNotEmptyString]
    public string $username;

    #[IsEmail]
    public string $email;

    #[IsPassword(min: 8)]
    public string $password;
}
