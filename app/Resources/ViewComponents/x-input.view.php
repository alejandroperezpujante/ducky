<?php

declare(strict_types=1);

/**
 * @var string $name
 * @var string|null $label
 * @var string|null $id
 * @var string|null $type
 * @var string|null $default
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
$type ??= 'text';
$default ??= null;

$errors = $formSession->getErrorsFor($name);
$original = $formSession->getOriginalValueFor($name, $default);
?>

<div>
    <label :for="$id" class="block text-sm font-semibold text-ink-700 mb-1">{{ $label }}</label>

    <textarea
        :if="$type === 'textarea'"
        :name="$name"
        :id="$id"
        class="w-full rounded-xl border border-cream-200 px-4 py-2.5 text-sm text-ink-800 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-coral-400 focus:border-transparent placeholder:text-ink-300 resize-none"
    >{{ $original }}</textarea>
    <input
        :else
        :type="$type"
        :name="$name"
        :id="$id"
        :value="$original"
        class="w-full rounded-xl border border-cream-200 px-4 py-2.5 text-sm text-ink-800 bg-cream-50 focus:outline-none focus:ring-2 focus:ring-coral-400 focus:border-transparent placeholder:text-ink-300"
    />

    <ul :if="$errors !== []" class="mt-1 space-y-0.5">
        <li :foreach="$errors as $error" class="text-xs text-red-600">
            {{ $validator->getErrorMessage($error) }}
        </li>
    </ul>
</div>
