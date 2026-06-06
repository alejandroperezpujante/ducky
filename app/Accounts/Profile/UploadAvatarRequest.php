<?php

declare(strict_types=1);

namespace App\Accounts\Profile;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;

/** Carries only file uploads; avatar is accessed via $request->files['avatar']. */
final class UploadAvatarRequest implements Request
{
    use IsRequest;
}
