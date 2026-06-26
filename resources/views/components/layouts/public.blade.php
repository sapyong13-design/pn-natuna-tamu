<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $title ?? 'Portal Tamu PN Natuna' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-pn-natuna-emblem.png') }}">
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
    <main class="portal-shell">
        {{ $slot }}
    </main>
</body>
</html>
