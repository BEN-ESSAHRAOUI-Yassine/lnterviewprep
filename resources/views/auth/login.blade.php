@extends('layouts.guest')
@section('title', 'Connexion')

@section('content')
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-4">
            <label for="email" class="block text-xs font-mono text-[var(--text-muted)] uppercase tracking-wider mb-2">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                class="w-full bg-[var(--bg-elevated)] border border-[var(--border)] rounded-md px-4 py-2.5 text-sm font-mono text-[var(--text-primary)] focus:border-[var(--accent)] focus:outline-none transition-colors @error('email') border-[var(--status-review)] @enderror"
            />
            @error('email')
                <p class="text-[var(--status-review)] text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="password" class="block text-xs font-mono text-[var(--text-muted)] uppercase tracking-wider mb-2">Mot de passe</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                class="w-full bg-[var(--bg-elevated)] border border-[var(--border)] rounded-md px-4 py-2.5 text-sm font-mono text-[var(--text-primary)] focus:border-[var(--accent)] focus:outline-none transition-colors @error('password') border-[var(--status-review)] @enderror"
            />
            @error('password')
                <p class="text-[var(--status-review)] text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="w-full bg-[var(--accent)] hover:bg-[var(--accent-soft)] text-white font-mono text-sm px-6 py-2.5 rounded-md transition-colors"
        >
            Se connecter
        </button>

        <p class="text-center text-[var(--text-muted)] text-sm mt-6">
            Pas de compte ?
            <a href="{{ route('register') }}" class="text-[var(--accent)] hover:text-[var(--accent-soft)] font-mono transition-colors">
                S'inscrire
            </a>
        </p>
    </form>
@endsection