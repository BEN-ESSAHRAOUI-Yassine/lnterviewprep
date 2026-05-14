@extends('layouts.app')

@section('title', 'Concepts - ' . $domain->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('domains.index') }}" class="text-[#8b90a7] hover:text-[#f1f3f9] text-sm font-mono">← Domaines</a>
    <span class="text-[#8b90a7] text-sm font-mono"> / </span>
    <span class="text-[#f1f3f9] text-sm font-mono">{{ $domain->name }}</span>
</div>

<div class="flex justify-between items-center mb-6">
    <div class="flex items-center gap-3">
        <span class="w-4 h-4 rounded-full" style="background-color: {{ $domain->color }}"></span>
        <h1 class="text-2xl font-mono font-semibold text-[#f1f3f9]">{{ $domain->name }}</h1>
    </div>
    <a href="{{ route('domains.concepts.create', $domain) }}" class="bg-[#6366f1] hover:bg-[#4f46e5] text-white font-mono text-sm px-5 py-2.5 rounded-md transition-colors">
        + Nouveau concept
    </a>
</div>

<form method="GET" action="{{ route('domains.concepts.index', $domain) }}" class="flex gap-4 mb-6 p-4 bg-[#1a1d27] border border-[#2e3245] rounded-md">
    <div>
        <label class="block text-xs font-mono text-[#8b90a7] mb-1">Statut</label>
        <select name="status" class="bg-[#242736] border border-[#2e3245] rounded-md px-3 py-2 text-sm font-mono text-[#f1f3f9]">
            <option value="">Tous</option>
            <option value="to_review" {{ request('status') === 'to_review' ? 'selected' : '' }}>À revoir</option>
            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>En cours</option>
            <option value="mastered" {{ request('status') === 'mastered' ? 'selected' : '' }}>Maîtrisé</option>
        </select>
    </div>
    <div>
        <label class="block text-xs font-mono text-[#8b90a7] mb-1">Difficulté</label>
        <select name="difficulty" class="bg-[#242736] border border-[#2e3245] rounded-md px-3 py-2 text-sm font-mono text-[#f1f3f9]">
            <option value="">Toutes</option>
            <option value="junior" {{ request('difficulty') === 'junior' ? 'selected' : '' }}>Junior</option>
            <option value="mid" {{ request('difficulty') === 'mid' ? 'selected' : '' }}>Mid</option>
            <option value="senior" {{ request('difficulty') === 'senior' ? 'selected' : '' }}>Senior</option>
        </select>
    </div>
    <div class="flex items-end">
        <button type="submit" class="bg-[#6366f1] hover:bg-[#4f46e5] text-white font-mono text-sm px-4 py-2 rounded-md transition-colors">Filtrer</button>
    </div>
</form>

@if($concepts->isEmpty())
<div class="text-center py-16 border border-dashed border-[#2e3245] rounded-md">
    <p class="text-4xl mb-4">✍️</p>
    <p class="font-mono text-[#8b90a7] mb-2">Ce domaine n'a pas encore de concepts.</p>
    <a href="{{ route('domains.concepts.create', $domain) }}" class="bg-[#6366f1] hover:bg-[#4f46e5] text-white font-mono text-sm px-5 py-2.5 rounded-md transition-colors inline-block mt-4">
        Ajouter un concept
    </a>
</div>
@else
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
            @foreach($concepts as $concept)
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
                    <form method="POST" action="{{ route('domains.concepts.updateStatus', [$domain, $concept]) }}">
                        @csrf
                        @method('PATCH')
                        <select name="status" onchange="this.form.submit()"
                                class="bg-[#242736] border border-[#2e3245] text-sm font-mono rounded px-2 py-1
                                       {{ $concept->status === 'mastered' ? 'text-[#22c55e]' :
                                          ($concept->status === 'in_progress' ? 'text-[#f59e0b]' : 'text-[#ef4444]') }}">
                            <option value="to_review" {{ $concept->status === 'to_review' ? 'selected' : '' }}>À revoir</option>
                            <option value="in_progress" {{ $concept->status === 'in_progress' ? 'selected' : '' }}>En cours</option>
                            <option value="mastered" {{ $concept->status === 'mastered' ? 'selected' : '' }}>Maîtrisé</option>
                        </select>
                    </form>
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

@if($archivedConcepts->isNotEmpty())
<div class="mt-8">
    <form method="GET" action="{{ route('domains.concepts.index', $domain) }}" class="mb-4">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="show_archived" value="1" {{ $showArchived ? 'checked' : '' }} onchange="this.form.submit()" class="w-4 h-4 rounded border-[#2e3245] bg-[#242736] text-[#6366f1]">
            <span class="text-sm font-mono text-[#8b90a7]">Afficher les concepts archivés</span>
        </label>
    </form>

    @if($showArchived)
    <div class="bg-[#1a1d27] border border-[#2e3245] rounded-md overflow-hidden opacity-75">
        <div class="bg-[#242736] px-4 py-2 border-b border-[#2e3245]">
            <h3 class="text-sm font-mono font-semibold text-[#8b90a7]">Concepts archivés</h3>
        </div>
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
                        <form method="POST" action="{{ route('domains.concepts.restore', [$domain, $concept->id]) }}" class="inline mr-2">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs font-mono text-[#22c55e] hover:text-[#22c55e]">Restaurer</button>
                        </form>
                        <form method="POST" action="{{ route('domains.concepts.forceDelete', [$domain, $concept->id]) }}" class="inline">
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
    @endif
</div>
@else
<form method="GET" action="{{ route('domains.concepts.index', $domain) }}" class="mb-4">
    <label class="flex items-center gap-2 cursor-pointer">
        <input type="checkbox" name="show_archived" value="1" {{ $showArchived ? 'checked' : '' }} onchange="this.form.submit()" class="w-4 h-4 rounded border-[#2e3245] bg-[#242736] text-[#6366f1]">
        <span class="text-sm font-mono text-[#8b90a7]">Afficher les concepts archivés</span>
    </label>
</form>
@endif
@endsection