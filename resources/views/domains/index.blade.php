@extends('layouts.app')

@section('title', 'Mes Domaines')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl font-mono font-semibold text-[var(--text-primary)]">Mes Domaines</h1>
    <div class="flex gap-4">
        <a href="{{ route('domains.archived') }}" class="bg-transparent hover:bg-[var(--bg-elevated)] border border-[var(--border)] text-[var(--text-muted)] hover:text-[var(--text-primary)] font-mono text-sm px-4 py-2.5 rounded-md transition-colors">
            Archives
        </a>
        <a href="{{ route('domains.create') }}" class="bg-[var(--accent)] hover:bg-[var(--accent-soft)] text-white font-mono text-sm px-5 py-2.5 rounded-md transition-colors">
            + Créer un domaine
        </a>
    </div>
</div>

@if($domains->isEmpty())
<div class="text-center py-16">
    <p class="text-4xl mb-4">🗂️</p>
    <p class="font-mono text-[var(--text-muted)] mb-2">Aucun domaine pour l'instant.</p>
    <a href="{{ route('domains.create') }}" class="bg-[var(--accent)] hover:bg-[var(--accent-soft)] text-white font-mono text-sm px-5 py-2.5 rounded-md transition-colors inline-block mt-4">
        Créer mon premier domaine
    </a>
</div>
@else
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($domains as $domain)
    <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-md p-5 hover:border-[var(--accent)]/50 transition-colors">
        <div class="flex items-center gap-2 mb-3">
            <span class="w-3 h-3 rounded-full" style="background-color: {{ $domain->color }}"></span>
            <h3 class="font-mono font-semibold text-[var(--text-primary)]">{{ $domain->name }}</h3>
        </div>
        <p class="text-[var(--text-muted)] text-sm">
            <span class="text-[var(--text-primary)] font-mono">{{ $domain->concepts_count }}</span> concepts ·
            <span class="text-[var(--status-mastered)] font-mono">{{ $domain->mastered_count }}</span> maîtrisés
        </p>
        @if($domain->concepts_count > 0)
        <div class="w-full bg-[var(--bg-elevated)] rounded-full h-1.5 mt-2">
            <div class="bg-[var(--accent)] h-1.5 rounded-full transition-all" style="width: {{ ($domain->mastered_count / $domain->concepts_count) * 100 }}%"></div>
        </div>
        <p class="text-[var(--text-muted)] text-xs font-mono mt-1">{{ $domain->mastered_count }}/{{ $domain->concepts_count }} maîtrisés</p>
        @endif
        <div class="flex gap-2 mt-4 pt-4 border-t border-[var(--border)]">
            <a href="{{ route('domains.show', $domain) }}" class="bg-[var(--accent)] hover:bg-[var(--accent-soft)] text-white font-mono text-sm px-4 py-2 rounded-md transition-colors flex-1 text-center">Voir</a>
            <a href="{{ route('domains.edit', $domain) }}" class="bg-transparent hover:bg-[var(--bg-elevated)] border border-[var(--border)] text-[var(--text-muted)] hover:text-[var(--text-primary)] font-mono text-sm px-3 py-2 rounded-md transition-colors">✏</a>
            <form method="POST" action="{{ route('domains.destroy', $domain) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="bg-transparent hover:bg-[var(--bg-elevated)] border border-[var(--border)] text-[var(--text-muted)] hover:text-[var(--status-review)] font-mono text-sm px-3 py-2 rounded-md transition-colors">🗑</button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection