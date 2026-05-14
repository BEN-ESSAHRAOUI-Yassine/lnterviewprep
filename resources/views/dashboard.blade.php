@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl font-mono font-semibold text-[#f1f3f9]">Bonjour, {{ auth()->user()->name }}.</h1>
    <a href="{{ route('domains.create') }}" class="bg-[#6366f1] hover:bg-[#4f46e5] text-white font-mono text-sm px-5 py-2.5 rounded-md transition-colors">
        + Nouveau domaine
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-[#1a1d27] border border-[#2e3245] rounded-md p-5">
        <p class="text-[#8b90a7] text-xs font-mono uppercase tracking-widest">À revoir</p>
        <p class="text-4xl font-mono font-semibold text-[#ef4444] mt-1">{{ $stats['À revoir'] }}</p>
    </div>
    <div class="bg-[#1a1d27] border border-[#2e3245] rounded-md p-5">
        <p class="text-[#8b90a7] text-xs font-mono uppercase tracking-widest">En cours</p>
        <p class="text-4xl font-mono font-semibold text-[#f59e0b] mt-1">{{ $stats['En cours'] }}</p>
    </div>
    <div class="bg-[#1a1d27] border border-[#2e3245] rounded-md p-5">
        <p class="text-[#8b90a7] text-xs font-mono uppercase tracking-widest">Maîtrisés</p>
        <p class="text-4xl font-mono font-semibold text-[#22c55e] mt-1">{{ $stats['Maîtrisé'] }}</p>
        @if($totalConcepts > 0)
        <div class="w-full bg-[#242736] rounded-full h-1.5 mt-2">
            <div class="bg-[#22c55e] h-1.5 rounded-full transition-all" style="width: {{ ($stats['Maîtrisé'] / $totalConcepts) * 100 }}%"></div>
        </div>
        @endif
    </div>
</div>

@if($bestDomain || $mostToReviewDomain)
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    @if($bestDomain)
    <div class="bg-[#1a1d27] border border-[#2e3245] rounded-md p-5">
        <p class="text-[#8b90a7] text-xs font-mono uppercase tracking-widest mb-2">🏆 Mieux maîtrisé</p>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full" style="background-color: {{ $bestDomain->color }}"></span>
            <span class="text-[#f1f3f9] font-mono font-semibold">{{ $bestDomain->name }}</span>
        </div>
        <p class="text-[#8b90a7] text-sm mt-1">{{ $bestDomain->mastered_count }} / {{ $bestDomain->concepts_count }} maîtrisés</p>
        @if($bestDomain->concepts_count > 0)
        <div class="w-full bg-[#242736] rounded-full h-1.5 mt-2">
            <div class="bg-[#6366f1] h-1.5 rounded-full transition-all" style="width: {{ ($bestDomain->mastered_count / $bestDomain->concepts_count) * 100 }}%"></div>
        </div>
        @endif
    </div>
    @endif

    @if($mostToReviewDomain && $mostToReviewDomain->to_review_count > 0)
    <div class="bg-[#1a1d27] border border-[#2e3245] rounded-md p-5">
        <p class="text-[#8b90a7] text-xs font-mono uppercase tracking-widest mb-2">⚠️ Priorité</p>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full" style="background-color: {{ $mostToReviewDomain->color }}"></span>
            <span class="text-[#f1f3f9] font-mono font-semibold">{{ $mostToReviewDomain->name }}</span>
        </div>
        <p class="text-[#ef4444] text-sm mt-1">{{ $mostToReviewDomain->to_review_count }} à revoir</p>
    </div>
    @endif
</div>
@endif

<div class="mb-4">
    <h2 class="text-lg font-mono font-semibold text-[#f1f3f9]">Domaines</h2>
</div>

@if($totalDomains === 0)
<div class="text-center py-16 border border-dashed border-[#2e3245] rounded-md">
    <p class="text-4xl mb-4">🗂️</p>
    <p class="font-mono text-[#8b90a7] mb-2">Aucun domaine pour l'instant.</p>
    <a href="{{ route('domains.create') }}" class="bg-[#6366f1] hover:bg-[#4f46e5] text-white font-mono text-sm px-5 py-2.5 rounded-md transition-colors inline-block mt-4">
        Créer mon premier domaine
    </a>
</div>
@else
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach(\App\Models\Domain::where('user_id', auth()->id())->withCount(['concepts', 'concepts as mastered_count' => fn($q) => $q->where('status', 'mastered')])->get() as $domain)
    <div class="bg-[#1a1d27] border border-[#2e3245] rounded-md p-5 hover:border-[#6366f1]/50 transition-colors">
        <div class="flex items-center gap-2 mb-3">
            <span class="w-3 h-3 rounded-full" style="background-color: {{ $domain->color }}"></span>
            <h3 class="font-mono font-semibold text-[#f1f3f9]">{{ $domain->name }}</h3>
        </div>
        <p class="text-[#8b90a7] text-sm">
            <span class="text-[#f1f3f9] font-mono">{{ $domain->concepts_count }}</span> concepts ·
            <span class="text-[#22c55e] font-mono">{{ $domain->mastered_count }}</span> maîtrisés
        </p>
        @if($domain->concepts_count > 0)
        <div class="w-full bg-[#242736] rounded-full h-1.5 mt-2">
            <div class="bg-[#6366f1] h-1.5 rounded-full transition-all" style="width: {{ ($domain->mastered_count / $domain->concepts_count) * 100 }}%"></div>
        </div>
        <p class="text-[#8b90a7] text-xs font-mono mt-1">{{ $domain->mastered_count }}/{{ $domain->concepts_count }} maîtrisés</p>
        @endif
        <div class="flex gap-2 mt-4 pt-4 border-t border-[#2e3245]">
            <a href="{{ route('domains.show', $domain) }}" class="bg-[#6366f1] hover:bg-[#4f46e5] text-white font-mono text-sm px-4 py-2 rounded-md transition-colors flex-1 text-center">Voir</a>
            <a href="{{ route('domains.edit', $domain) }}" class="bg-transparent hover:bg-[#242736] border border-[#2e3245] text-[#8b90a7] hover:text-[#f1f3f9] font-mono text-sm px-3 py-2 rounded-md transition-colors">✏</a>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection