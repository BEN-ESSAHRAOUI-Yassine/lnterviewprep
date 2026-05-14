@extends('layouts.app')

@section('title', $domain->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('domains.index') }}" class="text-[var(--text-muted)] hover:text-[var(--text-primary)] text-sm font-mono">← Domaines</a>
    <span class="text-[var(--text-muted)] text-sm font-mono"> / </span>
    <span class="text-[var(--text-primary)] text-sm font-mono">{{ $domain->name }}</span>
</div>

<div class="flex items-center gap-3 mb-8">
    <span class="w-4 h-4 rounded-full" style="background-color: {{ $domain->color }}"></span>
    <h1 class="text-2xl font-mono font-semibold text-[var(--text-primary)]">{{ $domain->name }}</h1>
    <a href="{{ route('domains.edit', $domain) }}" class="text-[var(--text-muted)] hover:text-[var(--text-primary)] text-sm font-mono ml-4">✏</a>
    <form method="POST" action="{{ route('domains.destroy', $domain) }}" class="inline">
        @method('DELETE')
        @csrf
        <button type="submit" class="text-[var(--text-muted)] hover:text-[var(--status-review)] text-sm font-mono">🗑</button>
    </form>
</div>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-lg font-mono font-semibold text-[var(--text-primary)]">Concepts</h2>
    <a href="{{ route('domains.concepts.create', $domain) }}" class="bg-[var(--accent)] hover:bg-[var(--accent-soft)] text-white font-mono text-sm px-5 py-2.5 rounded-md transition-colors">
        + Nouveau concept
    </a>
</div>

@php
$showArchived = request()->boolean('show_archived');
$activeConcepts = $domain->concepts;
$archivedConcepts = $domain->concepts()->onlyTrashed()->get();
@endphp

@if($activeConcepts->isEmpty() && $archivedConcepts->isEmpty())
<div class="text-center py-16 border border-dashed border-[var(--border)] rounded-md">
    <p class="text-4xl mb-4">✍️</p>
    <p class="font-mono text-[var(--text-muted)] mb-2">Ce domaine n'a pas encore de concepts.</p>
    <p class="text-[var(--text-faint)] text-xs mt-1">Clique sur "Nouveau concept" pour commencer.</p>
</div>
@else
@if($activeConcepts->isNotEmpty())
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
            @foreach($activeConcepts as $concept)
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
                    <span class="font-mono text-xs px-2 py-0.5 rounded-sm font-semibold uppercase tracking-wide
                        {{ $concept->status === 'mastered' ? 'bg-green-900/50 text-green-300' :
                           ($concept->status === 'in_progress' ? 'bg-amber-900/50 text-amber-300' : 'bg-red-900/50 text-red-300') }}">
                        {{ $concept->statusLabel }}
                    </span>
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

<form method="GET" action="{{ route('domains.show', $domain) }}" class="mb-4">
    <label class="flex items-center gap-2 cursor-pointer">
        <input type="checkbox" name="show_archived" value="1" {{ $showArchived ? 'checked' : '' }} onchange="this.form.submit()" class="w-4 h-4 rounded border-[var(--border)] bg-[var(--bg-elevated)] text-[var(--accent)]">
        <span class="text-sm font-mono text-[var(--text-muted)]">Afficher les concepts archivés</span>
    </label>
</form>

@if($showArchived && $archivedConcepts->isNotEmpty())
<div class="opacity-75">
    <h3 class="text-sm font-mono font-semibold text-[var(--text-muted)] mb-3">Concepts archivés</h3>
    <div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-md overflow-hidden">
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
                        <form method="POST" action="{{ route('concepts.restore', [$domain, $concept->id]) }}" class="inline mr-2">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs font-mono text-[var(--status-mastered)] hover:text-[var(--status-mastered)]">Restaurer</button>
                        </form>
                        <form method="POST" action="{{ route('concepts.forceDelete', [$domain, $concept->id]) }}" class="inline">
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
</div>
@endif
@endif
@endsection