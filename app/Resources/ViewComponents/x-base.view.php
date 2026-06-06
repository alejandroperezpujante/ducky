<?php

/**
 * @var string|null $title The webpage's title
 * @var string|null $active Active nav item: 'home', 'feed', 'create', 'profile'
 */

$active ??= null;
?>

<!doctype html>
<html lang="en" class="scroll-smooth">
<head>
    <title>{{ $title ?? 'Tempest' }}</title>

    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    <x-slot name="head"/>

    <x-vite-tags />
</head>
<body class="relative">

<!-- Top blob: yellow → coral -->
<div class="fixed inset-0 -z-10 blur-3xl overflow-hidden transform-gpu pointer-events-none" aria-hidden="true">
    <div
        class="left-[calc(50%-8rem)] relative bg-gradient-to-tr from-yellow-200 to-coral-200 opacity-50 w-[28rem] sm:w-[60rem] aspect-[1155/678] rotate-[30deg] -translate-x-1/2"
        style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"
    ></div>
</div>

<div class="pb-24">
    <x-slot/>
</div>
<x-bottom-nav :active="$active"/>
<x-slot name="scripts"/>
</body>
</html>
