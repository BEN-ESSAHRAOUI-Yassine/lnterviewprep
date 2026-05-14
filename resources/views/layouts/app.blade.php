<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>InterviewPrep — @yield('title', '')</title>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[var(--bg-base)] text-[var(--text-primary)] min-h-screen font-sans antialiased">

    <nav class="h-14 bg-[var(--bg-surface)] border-b border-[var(--border)] flex items-center px-6 gap-6">
        <a href="{{ route('dashboard') }}" class="font-mono font-semibold text-[var(--accent)] text-sm tracking-wide">
            InterviewPrep
        </a>
        <a href="{{ route('domains.index') }}" class="text-sm text-[var(--text-muted)] hover:text-[var(--text-primary)] transition-colors">
            Domaines
        </a>
        <a href="{{ route('dashboard') }}" class="text-sm text-[var(--text-muted)] hover:text-[var(--text-primary)] transition-colors">
            Dashboard
        </a>
        <div class="ml-auto flex items-center gap-4">
            <button onclick="toggleTheme()" class="text-sm text-[var(--text-muted)] hover:text-[var(--text-primary)] font-mono transition-colors px-2 py-1 rounded hover:bg-[var(--bg-elevated)]">
                <span id="theme-icon">🌙</span>
            </button>
            <span class="text-sm text-[var(--text-muted)] font-mono">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button class="text-sm text-[var(--text-muted)] hover:text-[var(--text-primary)] font-mono transition-colors">
                    Déconnexion
                </button>
            </form>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-6 py-8">
        @if(session('success'))
            <div class="bg-[var(--status-mastered)]/10 border border-[var(--status-mastered)] text-[var(--status-mastered)] text-sm font-mono px-4 py-3 rounded-md mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-[var(--status-review)]/10 border border-[var(--status-review)] text-[var(--status-review)] text-sm font-mono px-4 py-3 rounded-md mb-4">
                {{ session('error') }}
            </div>
        @endif
        @yield('content')
    </main>

    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', theme);
            document.getElementById('theme-icon').textContent = theme === 'dark' ? '🌙' : '☀️';
        })();

        function toggleTheme() {
            const html = document.documentElement;
            const current = html.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
            document.getElementById('theme-icon').textContent = next === 'dark' ? '🌙' : '☀️';
        }
    </script>

</body>
</html>