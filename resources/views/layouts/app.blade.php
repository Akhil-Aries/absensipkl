<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sistem Absensi PKL' }}</title>
    <meta name="description" content="Sistem absensi PKL berbasis Laravel">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="background-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>

    <div class="container {{ $wide ?? false ? 'wide' : '' }}">
        @yield('content')
    </div>

    <div id="toast" class="toast">{{ session('success') ?? session('error') ?? '' }}</div>

    <script src="{{ asset('js/theme.js') }}"></script>
    @stack('scripts')

    @if (session('success'))
        <script>window.addEventListener('DOMContentLoaded', () => showToast(@json(session('success')), '#10b981'));</script>
    @endif
    @if (session('error'))
        <script>window.addEventListener('DOMContentLoaded', () => showToast(@json(session('error')), '#ef4444'));</script>
    @endif
</body>
</html>
