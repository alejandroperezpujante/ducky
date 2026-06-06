<?php

declare(strict_types=1);

namespace App\Accounts\Profile;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;
use Tempest\Validation\Rules\IsNotEmptyString;

final class ReportFormatRequest implements Request
{
    use IsRequest;

    /** One of: json, xml, text */
    #[IsNotEmptyString]
    public string $format;
}
