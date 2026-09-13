<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">

    <style>
        #page-loader {
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.25s ease;
        }
        #page-loader.loading {
            display: flex;
            opacity: 1;
        }

        .loader-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            transform: scale(0.9);
            animation: popIn 0.35s ease forwards;
        }
        @keyframes popIn {
            to { transform: scale(1); }
        }

        .loader-ring-wrap {
            position: relative;
            width: 64px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .loader-ring {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 4px solid transparent;
            border-top-color: #2563eb;
            border-right-color: #93c5fd;
            animation: spin 0.9s cubic-bezier(0.6, 0.2, 0.4, 0.8) infinite;
        }
        .loader-icon {
            font-size: 22px;
            animation: pulse 1.4s ease-in-out infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50%      { transform: scale(1.15); opacity: 0.7; }
        }

        .loader-title {
            margin-top: 18px;
            font-size: 15px;
            font-weight: 600;
            color: #1f2937;
            letter-spacing: 0.01em;
        }
        .loader-dots span {
            display: inline-block;
            width: 5px;
            height: 5px;
            margin: 0 2px;
            border-radius: 50%;
            background: #2563eb;
            animation: dotFade 1.2s ease-in-out infinite;
        }
        .loader-dots span:nth-child(2) { animation-delay: 0.2s; }
        .loader-dots span:nth-child(3) { animation-delay: 0.4s; }
        @keyframes dotFade {
            0%, 80%, 100% { opacity: 0.2; transform: translateY(0); }
            40%           { opacity: 1; transform: translateY(-3px); }
        }

        .btn-loading {
            pointer-events: none;
            opacity: 0.7;
        }
        .btn-spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255,255,255,0.5);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            margin-right: 6px;
            vertical-align: -2px;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    <div id="page-loader">
        <div class="loader-card">
            <div class="loader-ring-wrap">
                <div class="loader-ring"></div>
                <span class="loader-icon"></span>
            </div>
            <p class="loader-title">LOADING...</p>
            <div class="loader-dots">
                <span></span><span></span><span></span>
            </div>
        </div>
    </div>

    <script>
        const loader = document.getElementById('page-loader');

        function startLoading() {
            loader.classList.add('loading');
        }

        function stopLoading() {
            loader.classList.remove('loading');
        }

        document.addEventListener('click', function (e) {
            const link = e.target.closest('a[href]');
            if (!link) return;
            if (link.target === '_blank') return;
            if (link.href.startsWith('javascript:')) return;
            if (link.getAttribute('href').startsWith('#')) return;
            if (e.metaKey || e.ctrlKey) return;

            startLoading();
        });

        document.addEventListener('submit', function (e) {
            startLoading();

            const btn = e.target.querySelector('button[type="submit"]');
            if (btn && !btn.classList.contains('btn-loading')) {
                btn.dataset.originalText = btn.innerHTML;
                btn.classList.add('btn-loading');
                btn.innerHTML = '<span class="btn-spinner"></span>Please wait...';
            }
        });

        window.addEventListener('pageshow', function () {
            stopLoading();
        });

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js');
            });
        }
    </script>
</body>
</html>