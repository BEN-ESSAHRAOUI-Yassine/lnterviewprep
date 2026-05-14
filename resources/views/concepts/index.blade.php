@extends('layouts.app')

@section('title', 'Concepts - ' . $domain->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('domains.index') }}" class="text-[var(--text-muted)] hover:text-[var(--text-primary)] text-sm font-mono">← Domaines</a>
    <span class="text-[var(--text-muted)] text-sm font-mono"> / </span>
    <span class="text-[var(--text-primary)] text-sm font-mono">{{ $domain->name }}</span>
</div>

<div class="flex justify-between items-center mb-6">
    <div class="flex items-center gap-3">
        <span class="w-4 h-4 rounded-full" style="background-color: {{ $domain->color }}"></span>
        <h1 class="text-2xl font-mono font-semibold text-[var(--text-primary)]">{{ $domain->name }}</h1>
    </div>
    <a href="{{ route('domains.concepts.create', $domain) }}" class="bg-[var(--accent)] hover:bg-[var(--accent-soft)] text-white font-mono text-sm px-5 py-2.5 rounded-md transition-colors">
        + Nouveau concept
    </a>
</div>

<form method="GET" action="{{ route('domains.concepts.index', $domain) }}" class="flex gap-4 mb-6 p-4 bg-[var(--bg-surface)] border border-[var(--border)] rounded-md">
    <div>
        <label class="block text-xs font-mono text-[var(--text-muted)] mb-1">Statut</label>
        <select name="status" class="bg-[var(--bg-elevated)] border border-[var(--border)] rounded-md px-3 py-2 text-sm font-mono text-[var(--text-primary)]">
            <option value="">Tous</option>
            <option value="to_review" {{ request('status') === 'to_review' ? 'selected' : '' }}>À revoir</option>
            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>En cours</option>
            <option value="mastered" {{ request('status') === 'mastered' ? 'selected' : '' }}>Maîtrisé</option>
        </select>
    </div>
    <div>
        <label class="block text-xs font-mono text-[var(--text-muted)] mb-1">Difficulté</label>
        <select name="difficulty" class="bg-[var(--bg-elevated)] border border-[var(--border)] rounded-md px-3 py-2 text-sm font-mono text-[var(--text-primary)]">
            <option value="">Toutes</option>
            <option value="junior" {{ request('difficulty') === 'junior' ? 'selected' : '' }}>Junior</option>
            <option value="mid" {{ request('difficulty') === 'mid' ? 'selected' : '' }}>Mid</option>
            <option value="senior" {{ request('difficulty') === 'senior' ? 'selected' : '' }}>Senior</option>
        </select>
    </div>
    <div class="flex items-end">
        <button type="submit" class="bg-[var(--accent)] hover:bg-[var(--accent-soft)] text-white font-mono text-sm px-4 py-2 rounded-md transition-colors">Filtrer</button>
    </div>
</form>

@if($concepts->isEmpty())
<div class="text-center py-16 border border-dashed border-[var(--border)] rounded-md">
    <p class="text-4xl mb-4">✍️</p>
    <p class="font-mono text-[var(--text-muted)] mb-2">Ce domaine n'a pas encore de concepts.</p>
    <a href="{{ route('domains.concepts.create', $domain) }}" class="bg-[var(--accent)] hover:bg-[var(--accent-soft)] text-white font-mono text-sm px-5 py-2.5 rounded-md transition-colors inline-block mt-4">
        Ajouter un concept
    </a>
</div>
@else
<div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-md overflow-hidden mb-8">
    <table class="w-full">
        <thead class="bg-[var(--bg-elevated)]">
            <tr>
                <th class="text-left text-xs font-mono text-[var(--text-muted)] uppercase tracking-widest px-4 py-3">Titre</th>
                <th class="text-left text-xs font-mono text-[var(--text-muted)] uppercase tracking-widest px-4 py-3">Niveau</th>
                <th class="text-left text-xs font-mono text-[var(--text-muted)] uppercase tracking-widest px-4 py-3">Statut</th>
                <th class="text-right text-xs font-mono text-[var(--text-muted)] uppercase tracking-widest px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($concepts as $concept)
            <tr class="border-b border-[var(--border)] hover:bg-[var(--bg-elevated)] transition-colors">
                <td class="py-3 px-4">
                    <a href="{{ route('domains.concepts.show', [$domain, $concept]) }}" class="text-[var(--text-primary)] font-medium hover:text-[var(--accent)]">
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
                                class="bg-[var(--bg-elevated)] border border-[var(--border)] text-sm font-mono rounded px-2 py-1
                                       {{ $concept->status === 'mastered' ? 'text-[var(--status-mastered)]' :
                                          ($concept->status === 'in_progress' ? 'text-[var(--status-progress)]' : 'text-[var(--status-review)]') }}">
                            <option value="to_review" {{ $concept->status === 'to_review' ? 'selected' : '' }}>À revoir</option>
                            <option value="in_progress" {{ $concept->status === 'in_progress' ? 'selected' : '' }}>En cours</option>
                            <option value="mastered" {{ $concept->status === 'mastered' ? 'selected' : '' }}>Maîtrisé</option>
                        </select>
                    </form>
                </td>
                <td class="py-3 px-4 text-right">
                    <div class="flex gap-2 justify-end">
                        <a href="{{ route('domains.concepts.show', [$domain, $concept]) }}" class="p-1.5 rounded hover:bg-[var(--bg-elevated)] text-[var(--text-muted)] hover:text-[var(--text-primary)] transition-colors">👁</a>
                        <a href="{{ route('domains.concepts.edit', [$domain, $concept]) }}" class="p-1.5 rounded hover:bg-[var(--bg-elevated)] text-[var(--text-muted)] hover:text-[var(--text-primary)] transition-colors">✏</a>
                        <form method="POST" action="{{ route('domains.concepts.destroy', [$domain, $concept]) }}" class="inline">
                            @method('DELETE')
                            @csrf
                            <button type="submit" class="p-1.5 rounded hover:bg-[var(--bg-elevated)] text-[var(--text-muted)] hover:text-[var(--status-review)] transition-colors">🗑</button>
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
            <input type="checkbox" name="show_archived" value="1" {{ $showArchived ? 'checked' : '' }} onchange="this.form.submit()" class="w-4 h-4 rounded border-[var(--border)] bg-[var(--bg-elevated)] text-[var(--accent)]">
            <span class="text-sm font-mono text-[var(--text-muted)]">Afficher les concepts archivés</span>
        </label>
    </form>

    @if($showArchived)
    <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-md overflow-hidden opacity-75">
        <div class="bg-[var(--bg-elevated)] px-4 py-2 border-b border-[var(--border)]">
            <h3 class="text-sm font-mono font-semibold text-[var(--text-muted)]">Concepts archivés</h3>
        </div>
        <table class="w-full">
            <tbody>
                @foreach($archivedConcepts as $concept)
                <tr class="border-b border-[var(--border)]">
                    <td class="py-3 px-4">
                        <span class="text-[var(--text-muted)]">{{ $concept->title }}</span>
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
                            <button type="submit" class="text-xs font-mono text-[var(--status-mastered)] hover:text-[var(--status-mastered)]">Restaurer</button>
                        </form>
                        <form method="POST" action="{{ route('domains.concepts.forceDelete', [$domain, $concept->id]) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-mono text-[var(--status-review)] hover:text-[var(--status-review)]">Supprimer définitivement</button>
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
        <input type="checkbox" name="show_archived" value="1" {{ $showArchived ? 'checked' : '' }} onchange="this.form.submit()" class="w-4 h-4 rounded border-[var(--border)] bg-[var(--bg-elevated)] text-[var(--accent)]">
        <span class="text-sm font-mono text-[var(--text-muted)]">Afficher les concepts archivés</span>
    </label>
</form>
@endif
@endsection