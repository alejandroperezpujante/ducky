<?php

declare(strict_types=1);

/**
 * @var string|null $action
 * @var string|Method|null $method
 * @var string|null $enctype
 */

use Tempest\Http\Method;

$action ??= null;
$method ??= Method::POST;

if ($method instanceof Method) {
    $method = $method->value;
}

$needsSpoofing = Method::trySpoofingFrom($method) instanceof Method;
$formMethod = $needsSpoofing ? 'POST' : $method;
?>

<form :action="$action" :method="$formMethod" :enctype="$enctype">
    <input
        :if="$needsSpoofing"
        type="hidden"
        name="_method"
        value="<?= htmlspecialchars($method) ?>"
    >

    <x-slot />
</form>
