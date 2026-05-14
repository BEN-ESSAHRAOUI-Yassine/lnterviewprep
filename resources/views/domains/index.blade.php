@extends('layouts.app')

@section('title', 'Mes Domaines')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl font-mono font-semibold text-[#f1f3f9]">Mes Domaines</h1>
    <div class="flex gap-4">
        <a href="{{ route('domains.archived') }}" class="bg-transparent hover:bg-[#242736] border border-[#2e3245] text-[#8b90a7] hover:text-[#f1f3f9] font-mono text-sm px-4 py-2.5 rounded-md transition-colors">
            Archives
        </a>
        <a href="{{ route('domains.create') }}" class="bg-[#6366f1] hover:bg-[#4f46e5] text-white font-mono text-sm px-5 py-2.5 rounded-md transition-colors">
            + Créer un domaine
        </a>
    </div>
</div>

@if($domains->isEmpty())
<div class="text-center py-16">
    <p class="text-4xl mb-4">🗂️</p>
    <p class="font-mono text-[#8b90a7] mb-2">Aucun domaine pour l'instant.</p>
    <a href="{{ route('domains.create') }}" class="bg-[#6366f1] hover:bg-[#4f46e5] text-white font-mono text-sm px-5 py-2.5 rounded-md transition-colors inline-block mt-4">
        Créer mon premier domaine
    </a>
</div>
@else
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($domains as $domain)
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
            <form method="POST" action="{{ route('domains.destroy', $domain) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="bg-transparent hover:bg-[#242736] border border-[#2e3245] text-[#8b90a7] hover:text-[#ef4444] font-mono text-sm px-3 py-2 rounded-md transition-colors">🗑</button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection