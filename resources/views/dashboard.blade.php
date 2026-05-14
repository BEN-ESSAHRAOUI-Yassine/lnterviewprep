@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl font-mono font-semibold text-[var(--text-primary)]">Bonjour, {{ auth()->user()->name }}.</h1>
    <a href="{{ route('domains.create') }}" class="bg-[var(--accent)] hover:bg-[var(--accent-soft)] text-white font-mono text-sm px-5 py-2.5 rounded-md transition-colors">
        + Nouveau domaine
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-md p-5">
        <p class="text-[var(--text-muted)] text-xs font-mono uppercase tracking-widest">À revoir</p>
        <p class="text-4xl font-mono font-semibold text-[var(--status-review)] mt-1">{{ $stats['À revoir'] }}</p>
    </div>
    <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-md p-5">
        <p class="text-[var(--text-muted)] text-xs font-mono uppercase tracking-widest">En cours</p>
        <p class="text-4xl font-mono font-semibold text-[var(--status-progress)] mt-1">{{ $stats['En cours'] }}</p>
    </div>
    <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-md p-5">
        <p class="text-[var(--text-muted)] text-xs font-mono uppercase tracking-widest">Maîtrisés</p>
        <p class="text-4xl font-mono font-semibold text-[var(--status-mastered)] mt-1">{{ $stats['Maîtrisé'] }}</p>
        @if($totalConcepts > 0)
        <div class="w-full bg-[var(--bg-elevated)] rounded-full h-1.5 mt-2">
            <div class="bg-[var(--status-mastered)] h-1.5 rounded-full transition-all" style="width: {{ ($stats['Maîtrisé'] / $totalConcepts) * 100 }}%"></div>
        </div>
        @endif
    </div>
</div>

@if($bestDomain || $mostToReviewDomain)
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    @if($bestDomain)
    <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-md p-5">
        <p class="text-[var(--text-muted)] text-xs font-mono uppercase tracking-widest mb-2">🏆 Mieux maîtrisé</p>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full" style="background-color: {{ $bestDomain->color }}"></span>
            <span class="text-[var(--text-primary)] font-mono font-semibold">{{ $bestDomain->name }}</span>
        </div>
        <p class="text-[var(--text-muted)] text-sm mt-1">{{ $bestDomain->mastered_count }} / {{ $bestDomain->concepts_count }} maîtrisés</p>
        @if($bestDomain->concepts_count > 0)
        <div class="w-full bg-[var(--bg-elevated)] rounded-full h-1.5 mt-2">
            <div class="bg-[var(--accent)] h-1.5 rounded-full transition-all" style="width: {{ ($bestDomain->mastered_count / $bestDomain->concepts_count) * 100 }}%"></div>
        </div>
        @endif
    </div>
    @endif

    @if($mostToReviewDomain && $mostToReviewDomain->to_review_count > 0)
    <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-md p-5">
        <p class="text-[var(--text-muted)] text-xs font-mono uppercase tracking-widest mb-2">⚠️ Priorité</p>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full" style="background-color: {{ $mostToReviewDomain->color }}"></span>
            <span class="text-[var(--text-primary)] font-mono font-semibold">{{ $mostToReviewDomain->name }}</span>
        </div>
        <p class="text-[var(--status-review)] text-sm mt-1">{{ $mostToReviewDomain->to_review_count }} à revoir</p>
    </div>
    @endif
</div>
@endif

<div class="mb-4">
    <h2 class="text-lg font-mono font-semibold text-[var(--text-primary)]">Domaines</h2>
</div>

@if($totalDomains === 0)
<div class="text-center py-16 border border-dashed border-[var(--border)] rounded-md">
    <p class="text-4xl mb-4">🗂️</p>
    <p class="font-mono text-[var(--text-muted)] mb-2">Aucun domaine pour l'instant.</p>
    <a href="{{ route('domains.create') }}" class="bg-[var(--accent)] hover:bg-[var(--accent-soft)] text-white font-mono text-sm px-5 py-2.5 rounded-md transition-colors inline-block mt-4">
        Créer mon premier domaine
    </a>
</div>
@else
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach(\App\Models\Domain::where('user_id', auth()->id())->withCount(['concepts', 'concepts as mastered_count' => fn($q) => $q->where('status', 'mastered')])->get() as $domain)
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
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection