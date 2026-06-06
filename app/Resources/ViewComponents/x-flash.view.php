<?php

declare(strict_types=1);

/**
 * @var string|null $success Flash success message
 * @var string|null $error   Flash error message
 */

$success ??= null;
$error ??= null;
?>

<div :if="$success !== null" class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
    {{ $success }}
</div>

<div :if="$error !== null" class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
    {{ $error }}
</div>
