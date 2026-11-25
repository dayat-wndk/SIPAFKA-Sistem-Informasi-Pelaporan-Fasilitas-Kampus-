<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIPAFKA') — SIPAFKA FMIPA UHO</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sipafka.css') }}">
    @stack('styles')
</head>
<body>
    <div class="app-shell">
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        @include('partials.sidebar')

        <div class="app-main">
            @include('partials.topbar')

            <main class="app-content">
                @include('partials.flash')

                @hasSection('judul')
                    <x-page-header
                        :breadcrumb="trim($__env->yieldContent('breadcrumb'))"
                        :judul="trim($__env->yieldContent('judul'))"
                        :subjudul="trim($__env->yieldContent('subjudul'))"
                    >
                        @hasSection('aksi_header')
                            <x-slot:aksi_header>
                                @yield('aksi_header')
                            </x-slot:aksi_header>
                        @endif
                    </x-page-header>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
