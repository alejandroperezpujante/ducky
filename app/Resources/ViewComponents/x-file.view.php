<?php

/**
 * @var string $name
 * @var string|null $label
 * @var string|null $id
 * @var string|null $accept  Accepted file types, e.g. "image/*"
 * @var string|null $hint    Optional hint text (e.g. "Max 5 MB")
 */

use Tempest\Http\Session\FormSession;
use Tempest\Validation\Validator;

use function Tempest\Container\get;
use function Tempest\Support\str;

/** @var FormSession $formSession */
$formSession = get(FormSession::class);

/** @var Validator $validator */
$validator = get(Validator::class);

$label ??= str($name)->title();
$id ??= $name;
$accept ??= null;
$hint ??= null;

$errors = $formSession->getErrorsFor($name);
?>

<div>
    <label :for="$id" class="block text-sm font-semibold text-ink-700 mb-1">{{ $label }}</label>

    <input
        type="file"
        :name="$name"
        :id="$id"
        :accept="$accept"
        class="w-full text-sm text-ink-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-cream-100 file:text-ink-700 hover:file:bg-cream-200 cursor-pointer"
    />

    <p :if="$hint !== null" class="mt-1 text-xs text-ink-400">{{ $hint }}</p>

    <ul :if="$errors !== []" class="mt-1 space-y-0.5">
        <li :foreach="$errors as $error" class="text-xs text-red-600">
            {{ $validator->getErrorMessage($error) }}
        </li>
    </ul>
</div>
