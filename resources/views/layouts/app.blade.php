<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Admin - Sistem Absensi RFID')</title>
    <meta name="description" content="@yield('description', 'Dashboard Admin untuk Sistem Absensi RFID')">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen pt-32 isolate">
    @include('layouts.header')
    @include('layouts.navbar')

    {{-- ALERTS taruh di sini, di luar <main>. Bisa disembunyikan dengan @section('hide_alerts', true) di view --}}
    @unless (View::hasSection('hide_alerts'))
        @include('partials.alerts')
    @endunless

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
        @yield('content')
    </main>
</body>
</html>
