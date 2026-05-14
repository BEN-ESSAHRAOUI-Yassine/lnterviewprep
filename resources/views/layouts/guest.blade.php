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

    <main class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="font-mono font-semibold text-2xl text-[var(--accent)] tracking-wide mb-2">InterviewPrep</h1>
                <p class="text-[var(--text-muted)] text-sm">Prépare ton entretien.</p>
            </div>

            <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-md p-8">
                @if(session('error'))
                    <div class="bg-[var(--status-review)]/10 border border-[var(--status-review)] text-[var(--status-review)] text-xs font-mono px-4 py-3 rounded-md mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </main>

    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

</body>
</html>