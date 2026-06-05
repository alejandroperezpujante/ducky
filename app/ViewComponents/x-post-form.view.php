<?php

use Tempest\Http\Method;

/**
 * @var string $action              Form submission URL
 * @var string|Method|null $method  HTTP method (default POST, PATCH for update)
 * @var string|null $content        Pre-filled content value (edit mode)
 * @var string|null $submitLabel    Label for the submit button
 */
?>

<x-form :action="$action" :method="$method ?? Method::POST">
    <x-input name="content" type="textarea" :label="'Content'" :default="$content ?? null"/>
    <x-submit :label="$submitLabel ?? 'Save'"/>
</x-form>
