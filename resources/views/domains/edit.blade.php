@extends('layouts.app')

@section('title', 'Modifier le domaine')

@section('content')
<h1 class="text-2xl font-mono font-semibold text-[var(--text-primary)] mb-8">Modifier le domaine</h1>

<form method="POST" action="{{ route('domains.update', $domain) }}" class="max-w-md">
    @csrf
    @method('PATCH')

    <div class="mb-6">
        <label class="block text-sm font-mono text-[var(--text-muted)] mb-2">Nom du domaine</label>
        <input type="text" name="name" value="{{ $domain->name }}" required
            class="w-full bg-[var(--bg-elevated)] border border-[var(--border)] rounded-md px-4 py-2.5 text-sm font-mono text-[var(--text-primary)] focus:border-[var(--accent)] focus:outline-none">
        @error('name')
            <p class="text-[var(--status-review)] text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-8">
        <label class="block text-sm font-mono text-[var(--text-muted)] mb-2">Couleur du badge</label>
        <div class="flex gap-3">
            @foreach(['#3b82f6', '#22c55e', '#ef4444', '#a855f7', '#f97316', '#eab308', '#ec4899', '#6b7280'] as $color)
            <label class="cursor-pointer">
                <input type="radio" name="color" value="{{ $color }}" {{ $domain->color === $color ? 'checked' : '' }} class="sr-only peer">
                <span class="w-8 h-8 rounded-full block ring-2 ring-transparent peer-checked:ring-white peer-checked:ring-offset-2 peer-checked:ring-offset-[var(--bg-surface)]" style="background-color: {{ $color }}"></span>
            </label>
            @endforeach
        </div>
        @error('color')
            <p class="text-[var(--status-review)] text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex gap-4">
        <a href="{{ route('domains.index') }}" class="bg-transparent hover:bg-[var(--bg-elevated)] border border-[var(--border)] text-[var(--text-muted)] hover:text-[var(--text-primary)] font-mono text-sm px-5 py-2.5 rounded-md transition-colors">Annuler</a>
        <button type="submit" class="bg-[var(--accent)] hover:bg-[var(--accent-soft)] text-white font-mono text-sm px-5 py-2.5 rounded-md transition-colors">Enregistrer</button>
    </div>
</form>
@endsection