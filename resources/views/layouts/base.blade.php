@props(['bodyClass' => 'app-shell', 'extraCss' => []])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Uni Club') }}</title>

    {{-- Roboto + Neo-Brutalist editorial faces (Archivo Black / Roboto Condensed) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;1,400&family=Roboto+Condensed:wght@400;700;900&family=Archivo+Black&display=swap"
        rel="stylesheet">

    {{-- Custom design system (no Tailwind) --}}
    @php $css = array_merge(['resources/css/app.css', 'resources/css/brutal.css'], $extraCss); @endphp
    @vite(array_merge($css, ['resources/js/app.js']))
    <style>[x-cloak] { display: none !important; }</style>
</head>

<body class="{{ $bodyClass }}" x-data="{ mobileOpen: false }">
    {{ $slot }}
</body>

</html>
