<?php
/**
 * @var string|null $title The webpage's title
 */
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
<body>
<x-slot/>
<x-slot name="scripts"/>
</body>
</html>
