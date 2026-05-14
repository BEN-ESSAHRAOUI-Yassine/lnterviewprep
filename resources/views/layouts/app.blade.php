<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>InterviewPrep — @yield('title', '')</title>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0f1117] text-[#f1f3f9] min-h-screen font-sans antialiased">

    <nav class="h-14 bg-[#1a1d27] border-b border-[#2e3245] flex items-center px-6 gap-6">
        <a href="{{ route('dashboard') }}" class="font-mono font-semibold text-indigo-400 text-sm tracking-wide">
            InterviewPrep
        </a>
        <a href="{{ route('domains.index') }}" class="text-sm text-[#8b90a7] hover:text-white transition-colors">
            Domaines
        </a>
        <a href="{{ route('dashboard') }}" class="text-sm text-[#8b90a7] hover:text-white transition-colors">
            Dashboard
        </a>
        <div class="ml-auto">
            <span class="text-sm text-[#8b90a7] mr-4 font-mono">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button class="text-sm text-[#8b90a7] hover:text-white font-mono transition-colors">
                    Déconnexion
                </button>
            </form>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-6 py-8">
        @if(session('success'))
            <div class="bg-green-900/30 border border-green-700 text-green-300 text-sm font-mono px-4 py-3 rounded-md mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-900/30 border border-red-700 text-red-300 text-sm font-mono px-4 py-3 rounded-md mb-4">
                {{ session('error') }}
            </div>
        @endif
        @yield('content')
    </main>

</body>
</html>