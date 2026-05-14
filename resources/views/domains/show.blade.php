@extends('layouts.app')

@section('title', $domain->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('domains.index') }}" class="text-[#8b90a7] hover:text-[#f1f3f9] text-sm font-mono">← Domaines</a>
    <span class="text-[#8b90a7] text-sm font-mono"> / </span>
    <span class="text-[#f1f3f9] text-sm font-mono">{{ $domain->name }}</span>
</div>

<div class="flex items-center gap-3 mb-8">
    <span class="w-4 h-4 rounded-full" style="background-color: {{ $domain->color }}"></span>
    <h1 class="text-2xl font-mono font-semibold text-[#f1f3f9]">{{ $domain->name }}</h1>
    <a href="{{ route('domains.edit', $domain) }}" class="text-[#8b90a7] hover:text-[#f1f3f9] text-sm font-mono ml-4">✏</a>
    <form method="POST" action="{{ route('domains.destroy', $domain) }}" class="inline">
        @method('DELETE')
        @csrf
        <button type="submit" class="text-[#8b90a7] hover:text-[#ef4444] text-sm font-mono">🗑</button>
    </form>
</div>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-lg font-mono font-semibold text-[#f1f3f9]">Concepts</h2>
    <a href="{{ route('domains.concepts.create', $domain) }}" class="bg-[#6366f1] hover:bg-[#4f46e5] text-white font-mono text-sm px-5 py-2.5 rounded-md transition-colors">
        + Nouveau concept
    </a>
</div>

@php
$showArchived = request()->boolean('show_archived');
$activeConcepts = $domain->concepts;
$archivedConcepts = $showArchived ? $domain->concepts()->onlyTrashed()->get() : collect();
@endphp

@if($activeConcepts->isEmpty() && $archivedConcepts->isEmpty())
<div class="text-center py-16 border border-dashed border-[#2e3245] rounded-md">
    <p class="text-4xl mb-4">✍️</p>
    <p class="font-mono text-[#8b90a7] mb-2">Ce domaine n'a pas encore de concepts.</p>
    <p class="text-[#4a4f6a] text-xs mt-1">Clique sur "Nouveau concept" pour commencer.</p>
</div>
@else
@if($activeConcepts->isNotEmpty())
<div class="bg-[#1a1d27] border border-[#2e3245] rounded-md overflow-hidden mb-8">
    <table class="w-full">
        <thead class="bg-[#242736]">
            <tr>
                <th class="text-left text-xs font-mono text-[#8b90a7] uppercase tracking-widest px-4 py-3">Titre</th>
                <th class="text-left text-xs font-mono text-[#8b90a7] uppercase tracking-widest px-4 py-3">Niveau</th>
                <th class="text-left text-xs font-mono text-[#8b90a7] uppercase tracking-widest px-4 py-3">Statut</th>
                <th class="text-right text-xs font-mono text-[#8b90a7] uppercase tracking-widest px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activeConcepts as $concept)
            <tr class="border-b border-[#2e3245] hover:bg-[#242736] transition-colors">
                <td class="py-3 px-4">
                    <a href="{{ route('domains.concepts.show', [$domain, $concept]) }}" class="text-[#f1f3f9] font-medium hover:text-[#6366f1]">
                        {{ $concept->title }}
                    </a>
                </td>
                <td class="py-3 px-4">
                    <span class="font-mono text-xs px-2 py-0.5 rounded-sm font-semibold uppercase tracking-wide
                        {{ $concept->difficulty === 'junior' ? 'bg-sky-900/50 text-sky-300' :
                           ($concept->difficulty === 'mid' ? 'bg-violet-900/50 text-violet-300' : 'bg-rose-900/50 text-rose-300') }}">
                        {{ $concept->difficultyLabel }}
                    </span>
                </td>
                <td class="py-3 px-4">
                    <span class="font-mono text-xs px-2 py-0.5 rounded-sm font-semibold uppercase tracking-wide
                        {{ $concept->status === 'mastered' ? 'bg-green-900/50 text-green-300' :
                           ($concept->status === 'in_progress' ? 'bg-amber-900/50 text-amber-300' : 'bg-red-900/50 text-red-300') }}">
                        {{ $concept->statusLabel }}
                    </span>
                </td>
                <td class="py-3 px-4 text-right">
                    <div class="flex gap-2 justify-end">
                        <a href="{{ route('domains.concepts.show', [$domain, $concept]) }}" class="p-1.5 rounded hover:bg-[#242736] text-[#8b90a7] hover:text-[#f1f3f9] transition-colors">👁</a>
                        <a href="{{ route('domains.concepts.edit', [$domain, $concept]) }}" class="p-1.5 rounded hover:bg-[#242736] text-[#8b90a7] hover:text-[#f1f3f9] transition-colors">✏</a>
                        <form method="POST" action="{{ route('domains.concepts.destroy', [$domain, $concept]) }}" class="inline">
                            @method('DELETE')
                            @csrf
                            <button type="submit" class="p-1.5 rounded hover:bg-[#242736] text-[#8b90a7] hover:text-[#ef4444] transition-colors">🗑</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<form method="GET" action="{{ route('domains.show', $domain) }}" class="mb-4">
    <label class="flex items-center gap-2 cursor-pointer">
        <input type="checkbox" name="show_archived" value="1" {{ $showArchived ? 'checked' : '' }} onchange="this.form.submit()" class="w-4 h-4 rounded border-[#2e3245] bg-[#242736] text-[#6366f1]">
        <span class="text-sm font-mono text-[#8b90a7]">Afficher les concepts archivés</span>
    </label>
</form>

@if($showArchived && $archivedConcepts->isNotEmpty())
<div class="opacity-75">
    <h3 class="text-sm font-mono font-semibold text-[#8b90a7] mb-3">Concepts archivés</h3>
    <div class="bg-[#1a1d27] border border-[#2e3245] rounded-md overflow-hidden">
        <table class="w-full">
            <tbody>
                @foreach($archivedConcepts as $concept)
                <tr class="border-b border-[#2e3245]">
                    <td class="py-3 px-4">
                        <span class="text-[#8b90a7]">{{ $concept->title }}</span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="font-mono text-xs px-2 py-0.5 rounded-sm font-semibold uppercase tracking-wide
                            {{ $concept->difficulty === 'junior' ? 'bg-sky-900/50 text-sky-300' :
                               ($concept->difficulty === 'mid' ? 'bg-violet-900/50 text-violet-300' : 'bg-rose-900/50 text-rose-300') }}">
                            {{ $concept->difficultyLabel }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-right">
                        <form method="POST" action="{{ route('concepts.restore', [$domain, $concept->id]) }}" class="inline mr-2">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs font-mono text-[#22c55e] hover:text-[#22c55e]">Restaurer</button>
                        </form>
                        <form method="POST" action="{{ route('concepts.forceDelete', [$domain, $concept->id]) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-mono text-[#ef4444] hover:text-[#ef4444]">Supprimer définitivement</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endif
@endsection