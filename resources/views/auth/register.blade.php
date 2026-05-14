@extends('layouts.guest')
@section('title', 'Inscription')

@section('content')
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-4">
            <label for="name" class="block text-xs font-mono text-[var(--text-muted)] uppercase tracking-wider mb-2">Nom</label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                class="w-full bg-[var(--bg-elevated)] border border-[var(--border)] rounded-md px-4 py-2.5 text-sm font-mono text-[var(--text-primary)] focus:border-[var(--accent)] focus:outline-none transition-colors @error('name') border-[var(--status-review)] @enderror"
            />
            @error('name')
                <p class="text-[var(--status-review)] text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="block text-xs font-mono text-[var(--text-muted)] uppercase tracking-wider mb-2">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="username"
                class="w-full bg-[var(--bg-elevated)] border border-[var(--border)] rounded-md px-4 py-2.5 text-sm font-mono text-[var(--text-primary)] focus:border-[var(--accent)] focus:outline-none transition-colors @error('email') border-[var(--status-review)] @enderror"
            />
            @error('email')
                <p class="text-[var(--status-review)] text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="block text-xs font-mono text-[var(--text-muted)] uppercase tracking-wider mb-2">Mot de passe</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                class="w-full bg-[var(--bg-elevated)] border border-[var(--border)] rounded-md px-4 py-2.5 text-sm font-mono text-[var(--text-primary)] focus:border-[var(--accent)] focus:outline-none transition-colors @error('password') border-[var(--status-review)] @enderror"
            />
            @error('password')
                <p class="text-[var(--status-review)] text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="password_confirmation" class="block text-xs font-mono text-[var(--text-muted)] uppercase tracking-wider mb-2">Confirmer le mot de passe</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                class="w-full bg-[var(--bg-elevated)] border border-[var(--border)] rounded-md px-4 py-2.5 text-sm font-mono text-[var(--text-primary)] focus:border-[var(--accent)] focus:outline-none transition-colors @error('password_confirmation') border-[var(--status-review)] @enderror"
            />
            @error('password_confirmation')
                <p class="text-[var(--status-review)] text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="w-full bg-[var(--accent)] hover:bg-[var(--accent-soft)] text-white font-mono text-sm px-6 py-2.5 rounded-md transition-colors"
        >
            S'inscrire
        </button>

        <p class="text-center text-[var(--text-muted)] text-sm mt-6">
            Déjà inscrit ?
            <a href="{{ route('login') }}" class="text-[var(--accent)] hover:text-[var(--accent-soft)] font-mono transition-colors">
                Se connecter
            </a>
        </p>
    </form>
@endsection