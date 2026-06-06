<?php

declare(strict_types=1);

use Tempest\Http\Method;

/**
 * @var string $action
 * @var string|Method|null $method
 * @var string|null $content        Pre-filled content value (view data from controller on edit)
 * @var string|null $submitLabel
 */

?>

<x-form :action="$action" :method="$method ?? Method::POST">
    <x-input name="content" type="textarea" :label="'Content'" :default="$content ?? null"/>
    <x-submit :label="$submitLabel ?? 'Save'"/>
</x-form>
