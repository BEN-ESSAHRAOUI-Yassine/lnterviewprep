@extends('layouts.guest')
@section('title', 'Connexion')

@section('content')
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-4">
            <label for="email" class="block text-xs font-mono text-[#8b90a7] uppercase tracking-wider mb-2">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                class="w-full bg-[#242736] border border-[#2e3245] rounded-md px-4 py-2.5 text-sm font-mono text-[#f1f3f9] focus:border-indigo-500 focus:outline-none transition-colors @error('email') border-red-500 @enderror"
            />
            @error('email')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="password" class="block text-xs font-mono text-[#8b90a7] uppercase tracking-wider mb-2">Mot de passe</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                class="w-full bg-[#242736] border border-[#2e3245] rounded-md px-4 py-2.5 text-sm font-mono text-[#f1f3f9] focus:border-indigo-500 focus:outline-none transition-colors @error('password') border-red-500 @enderror"
            />
            @error('password')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-mono text-sm px-6 py-2.5 rounded-md transition-colors"
        >
            Se connecter
        </button>

        <p class="text-center text-[#8b90a7] text-sm mt-6">
            Pas de compte ?
            <a href="{{ route('register') }}" class="text-indigo-400 hover:text-indigo-300 font-mono transition-colors">
                S'inscrire
            </a>
        </p>
    </form>
@endsection