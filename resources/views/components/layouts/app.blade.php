<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="icon" href="/storage/branding/favicon.png" type="image/png">

        <title>{{ $title ?? 'Page Title' }}</title>

        @vite('resources/css/filament/app/theme.css')
        @filamentStyles
        {{ filament()->getTheme()->getHtml() }}
        {{ filament()->getFontHtml() }}
    </head>
    <body>
        {{ $slot }}
    </body>
</html>
