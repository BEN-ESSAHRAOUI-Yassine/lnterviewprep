@extends('layouts.app')

@section('title', 'Domaines archivés')

@section('content')
<div class="mb-6">
    <a href="{{ route('domains.index') }}" class="text-[var(--text-muted)] hover:text-[var(--text-primary)] text-sm font-mono">← Domaines actifs</a>
</div>

<div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl font-mono font-semibold text-[var(--text-primary)]">Domaines archivés</h1>
</div>

@if($domains->isEmpty())
<div class="text-center py-16 border border-dashed border-[var(--border)] rounded-md">
    <p class="text-4xl mb-4">📦</p>
    <p class="font-mono text-[var(--text-muted)] mb-2">Aucun domaine archivé.</p>
    <a href="{{ route('domains.index') }}" class="text-[var(--accent)] hover:text-[var(--accent-soft)] font-mono text-sm">Retour aux domaines actifs</a>
</div>
@else
<div class="bg-[var(--bg-surface)] border border-[var(--border)] rounded-md overflow-hidden">
    <table class="w-full">
        <thead class="bg-[var(--bg-elevated)]">
            <tr>
                <th class="text-left text-xs font-mono text-[var(--text-muted)] uppercase tracking-widest px-4 py-3">Nom</th>
                <th class="text-left text-xs font-mono text-[var(--text-muted)] uppercase tracking-widest px-4 py-3">Couleur</th>
                <th class="text-left text-xs font-mono text-[var(--text-muted)] uppercase tracking-widest px-4 py-3">Archivé le</th>
                <th class="text-right text-xs font-mono text-[var(--text-muted)] uppercase tracking-widest px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($domains as $domain)
            <tr class="border-b border-[var(--border)] hover:bg-[var(--bg-elevated)] transition-colors">
                <td class="py-3 px-4">
                    <span class="text-[var(--text-primary)] font-mono">{{ $domain->name }}</span>
                </td>
                <td class="py-3 px-4">
                    <span class="w-3 h-3 rounded-full inline-block" style="background-color: {{ $domain->color }}"></span>
                </td>
                <td class="py-3 px-4">
                    <span class="text-[var(--text-muted)] text-sm font-mono">{{ $domain->deleted_at->format('d/m/Y à H:i') }}</span>
                </td>
                <td class="py-3 px-4 text-right">
                    <div class="flex gap-2 justify-end">
                        <form method="POST" action="{{ route('domains.restore', $domain->id) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="bg-transparent hover:bg-[var(--bg-elevated)] border border-[var(--border)] text-[var(--status-mastered)] hover:text-[var(--status-mastered)] font-mono text-xs px-3 py-1 rounded-md transition-colors">Restaurer</button>
                        </form>
                        <form method="POST" action="{{ route('domains.forceDelete', $domain->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-transparent hover:bg-[var(--bg-elevated)] border border-[var(--border)] text-[var(--status-review)] hover:text-[var(--status-review)] font-mono text-xs px-3 py-1 rounded-md transition-colors" onclick="return confirm('Êtes-vous sûr ? Cette action est irréversible.')">Supprimer définitivement</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection