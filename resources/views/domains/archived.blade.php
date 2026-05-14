@extends('layouts.app')

@section('title', 'Domaines archivés')

@section('content')
<div class="mb-6">
    <a href="{{ route('domains.index') }}" class="text-[#8b90a7] hover:text-[#f1f3f9] text-sm font-mono">← Domaines actifs</a>
</div>

<div class="flex justify-between items-center mb-8">
    <h1 class="text-2xl font-mono font-semibold text-[#f1f3f9]">Domaines archivés</h1>
</div>

@if($domains->isEmpty())
<div class="text-center py-16 border border-dashed border-[#2e3245] rounded-md">
    <p class="text-4xl mb-4">📦</p>
    <p class="font-mono text-[#8b90a7] mb-2">Aucun domaine archivé.</p>
    <a href="{{ route('domains.index') }}" class="text-[#6366f1] hover:text-[#4f46e5] font-mono text-sm">Retour aux domaines actifs</a>
</div>
@else
<div class="bg-[#1a1d27] border border-[#2e3245] rounded-md overflow-hidden">
    <table class="w-full">
        <thead class="bg-[#242736]">
            <tr>
                <th class="text-left text-xs font-mono text-[#8b90a7] uppercase tracking-widest px-4 py-3">Nom</th>
                <th class="text-left text-xs font-mono text-[#8b90a7] uppercase tracking-widest px-4 py-3">Couleur</th>
                <th class="text-left text-xs font-mono text-[#8b90a7] uppercase tracking-widest px-4 py-3">Archivé le</th>
                <th class="text-right text-xs font-mono text-[#8b90a7] uppercase tracking-widest px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($domains as $domain)
            <tr class="border-b border-[#2e3245] hover:bg-[#242736] transition-colors">
                <td class="py-3 px-4">
                    <span class="text-[#f1f3f9] font-mono">{{ $domain->name }}</span>
                </td>
                <td class="py-3 px-4">
                    <span class="w-3 h-3 rounded-full inline-block" style="background-color: {{ $domain->color }}"></span>
                </td>
                <td class="py-3 px-4">
                    <span class="text-[#8b90a7] text-sm font-mono">{{ $domain->deleted_at->format('d/m/Y à H:i') }}</span>
                </td>
                <td class="py-3 px-4 text-right">
                    <div class="flex gap-2 justify-end">
                        <form method="POST" action="{{ route('domains.restore', $domain->id) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="bg-transparent hover:bg-[#242736] border border-[#2e3245] text-[#22c55e] hover:text-[#22c55e] font-mono text-xs px-3 py-1 rounded-md transition-colors">Restaurer</button>
                        </form>
                        <form method="POST" action="{{ route('domains.forceDelete', $domain->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-transparent hover:bg-[#242736] border border-[#2e3245] text-[#ef4444] hover:text-[#ef4444] font-mono text-xs px-3 py-1 rounded-md transition-colors" onclick="return confirm('Êtes-vous sûr ? Cette action est irréversible.')">Supprimer définitivement</button>
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