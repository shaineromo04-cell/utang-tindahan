<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Utang sa Tindahan</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background-color: #fafafa;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }
        @keyframes spinReverse {
            from { transform: rotate(360deg); }
            to   { transform: rotate(0deg); }
        }
        @keyframes drift1 {
            0%, 100% { transform: translate(0, 0); }
            50%      { transform: translate(50px, 30px); }
        }
        @keyframes drift2 {
            0%, 100% { transform: translate(0, 0); }
            50%      { transform: translate(-40px, 40px); }
        }
        @keyframes drift3 {
            0%, 100% { transform: translate(0, 0); }
            50%      { transform: translate(30px, -50px); }
        }
        @keyframes bob {
            0%, 100% { transform: translateY(0); }
            50%      { transform: translateY(-25px); }
        }
        @keyframes pulseScale {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50%      { transform: scale(1.2); opacity: 0.8; }
        }

        .shape {
            position: absolute;
            z-index: 0;
        }

        .shape-square {
            width: 70px; height: 70px;
            border: 3px solid #bfdbfe;
            border-radius: 16px;
            top: 12%; left: 8%;
            animation: spin 20s linear infinite, drift1 14s ease-in-out infinite;
        }
        .shape-circle-outline {
            width: 100px; height: 100px;
            border: 3px solid #fed7aa;
            border-radius: 9999px;
            top: 65%; left: 12%;
            animation: drift2 16s ease-in-out infinite, pulseScale 6s ease-in-out infinite;
        }
        .shape-triangle {
            width: 0; height: 0;
            border-left: 35px solid transparent;
            border-right: 35px solid transparent;
            border-bottom: 60px solid #bbf7d0;
            top: 20%; right: 10%;
            animation: spinReverse 26s linear infinite, bob 10s ease-in-out infinite;
            opacity: 0.6;
        }
        .shape-dot-cluster {
            top: 75%; right: 15%;
            width: 90px; height: 90px;
            animation: drift3 18s ease-in-out infinite;
        }
        .shape-dot-cluster span {
            position: absolute;
            width: 8px; height: 8px;
            border-radius: 9999px;
            background: #c4b5fd;
        }
        .shape-blob {
            width: 260px; height: 260px;
            background: #dbeafe;
            border-radius: 40% 60% 55% 45% / 45% 40% 60% 55%;
            top: 40%; left: 45%;
            opacity: 0.5;
            animation: spin 40s linear infinite, pulseScale 9s ease-in-out infinite;
        }
        .shape-plus {
            width: 40px; height: 40px;
            top: 8%; right: 30%;
            animation: spin 15s linear infinite, bob 7s ease-in-out infinite;
        }
        .shape-plus::before, .shape-plus::after {
            content: '';
            position: absolute;
            background: #fbcfe8;
        }
        .shape-plus::before {
            width: 100%; height: 6px; top: 17px; left: 0;
        }
        .shape-plus::after {
            width: 6px; height: 100%; left: 17px; top: 0;
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-800 overflow-x-hidden">

    <div class="relative min-h-screen flex flex-col overflow-hidden">

        <div class="shape shape-blob"></div>
        <div class="shape shape-square"></div>
        <div class="shape shape-circle-outline"></div>
        <div class="shape shape-triangle"></div>
        <div class="shape shape-plus"></div>
        <div class="shape shape-dot-cluster">
            <span style="top: 0; left: 0;"></span>
            <span style="top: 0; left: 25px;"></span>
            <span style="top: 0; left: 50px;"></span>
            <span style="top: 25px; left: 0;"></span>
            <span style="top: 25px; left: 25px;"></span>
            <span style="top: 25px; left: 50px;"></span>
        </div>

        <div class="relative z-10 flex flex-col min-h-screen">

            <header class="border-b border-gray-100 bg-white/80 backdrop-blur-sm sticky top-0 z-20">
                <div class="max-w-5xl mx-auto px-4 py-4 flex justify-between items-center">
                    <span class="font-semibold text-lg text-gray-900">Utang sa Tindahan</span>
                    <div class="space-x-4 text-sm">
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Log in</a>
                            <a href="{{ route('register') }}"
                               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                Get Started
                            </a>
                        @endauth
                    </div>
                </div>
            </header>

            <main class="flex-1 flex items-center">
                <div class="max-w-5xl mx-auto px-4 py-16 text-center">
                    <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 leading-tight">
                        Track utang ng tindahan,<br class="hidden sm:block"> walang gulo.
                    </h1>
                    <p class="mt-4 text-lg text-gray-500 max-w-xl mx-auto">
                        Simpleng paraan para malaman kung sino may utang, magkano, at kung
                        kailan nagbayad — accessible sa phone, kahit saan ka pa.
                    </p>

                    <div class="mt-8 flex justify-center gap-3">
                        @auth
                            <a href="{{ route('customers.index') }}"
                               class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700">
                                View Customers
                            </a>
                        @else
                            <a href="{{ route('register') }}"
                               class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700">
                                Simulan Ngayon
                            </a>
                            <a href="{{ route('login') }}"
                               class="border border-gray-300 text-gray-700 px-6 py-3 rounded-lg font-medium hover:bg-gray-50">
                                Log in
                            </a>
                        @endauth
                    </div>
                </div>
            </main>

            <section class="border-t border-gray-100 bg-white/70 backdrop-blur-sm">
                <div class="max-w-5xl mx-auto px-4 py-12 grid grid-cols-1 sm:grid-cols-3 gap-8 text-center">
                    <div>
                        <p class="text-2xl font-semibold text-gray-900">📒</p>
                        <p class="mt-2 font-medium text-gray-800">Listahan ng Utang</p>
                        <p class="text-sm text-gray-500 mt-1">Bawat customer, kita agad ang balance.</p>
                    </div>
                    <div>
                        <p class="text-2xl font-semibold text-gray-900">💵</p>
                        <p class="mt-2 font-medium text-gray-800">Record ng Bayad</p>
                        <p class="text-sm text-gray-500 mt-1">Automatic na-a-apply sa pinakalumang utang.</p>
                    </div>
                    <div>
                        <p class="text-2xl font-semibold text-gray-900">📱</p>
                        <p class="mt-2 font-medium text-gray-800">Gamit kahit saan</p>
                        <p class="text-sm text-gray-500 mt-1">Buksan sa phone, tablet, o computer.</p>
                    </div>
                </div>
            </section>

            <footer class="text-center text-xs text-gray-400 py-6">
                © {{ date('Y') }} Utang sa Tindahan
            </footer>
        </div>
    </div>

</body>
</html>