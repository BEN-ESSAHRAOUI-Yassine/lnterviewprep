@extends('layouts.app')

@section('title', $concept->title)

@section('content')
<div class="mb-6">
    <a href="{{ route('domains.concepts.index', $domain) }}" class="text-[#8b90a7] hover:text-[#f1f3f9] text-sm font-mono">← {{ $domain->name }}</a>
    <span class="text-[#8b90a7] text-sm font-mono"> / </span>
    <span class="text-[#f1f3f9] text-sm font-mono">{{ $concept->title }}</span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div>
        <div class="flex items-center gap-3 mb-4">
            <span class="font-mono text-xs px-2 py-0.5 rounded-sm font-semibold uppercase tracking-wide
                {{ $concept->difficulty === 'junior' ? 'bg-sky-900/50 text-sky-300' :
                   ($concept->difficulty === 'mid' ? 'bg-violet-900/50 text-violet-300' : 'bg-rose-900/50 text-rose-300') }}">
                {{ $concept->difficultyLabel }}
            </span>
            <span class="font-mono text-xs px-2 py-0.5 rounded-sm font-semibold uppercase tracking-wide
                {{ $concept->status === 'mastered' ? 'bg-green-900/50 text-green-300' :
                   ($concept->status === 'in_progress' ? 'bg-amber-900/50 text-amber-300' : 'bg-red-900/50 text-red-300') }}">
                {{ $concept->statusLabel }}
            </span>
        </div>

        <h1 class="text-2xl font-mono font-semibold text-[#f1f3f9] mb-6">{{ $concept->title }}</h1>

        <div class="mb-6">
            <h2 class="text-sm font-mono text-[#8b90a7] uppercase tracking-widest mb-2">Explication</h2>
            <p class="text-[#f1f3f9] font-sans text-sm leading-relaxed whitespace-pre-wrap">{{ $concept->explanation }}</p>
        </div>

        <div class="flex gap-4 pt-4 border-t border-[#2e3245]">
            <a href="{{ route('domains.concepts.edit', [$domain, $concept]) }}" class="bg-transparent hover:bg-[#242736] border border-[#2e3245] text-[#8b90a7] hover:text-[#f1f3f9] font-mono text-sm px-4 py-2 rounded-md transition-colors">✏ Modifier</a>
            <form method="POST" action="{{ route('domains.concepts.destroy', [$domain, $concept]) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="bg-transparent hover:bg-[#242736] border border-[#2e3245] text-[#8b90a7] hover:text-[#ef4444] font-mono text-sm px-4 py-2 rounded-md transition-colors">🗑 Supprimer</button>
            </form>
        </div>
    </div>

    <div>
        <h2 class="text-lg font-mono font-semibold text-[#f1f3f9] mb-4">Questions d'entretien</h2>

        <form method="POST" action="{{ route('generated-questions.store', $concept) }}" class="mb-6">
            @csrf
            <button type="submit" class="flex items-center gap-2 bg-[#6366f1] hover:bg-[#4f46e5] text-white font-mono text-sm px-5 py-2.5 rounded-md transition-colors w-full justify-center">
                <span>⚡</span>
                Générer 5 questions d'entretien
            </button>
        </form>

        @if($concept->generatedQuestions->isEmpty())
        <div class="text-center py-8 border border-dashed border-[#2e3245] rounded-md">
            <p class="text-[#8b90a7] font-mono text-sm">Aucune génération pour ce concept.</p>
            <p class="text-[#4a4f6a] text-xs mt-1">Clique sur "Générer" pour démarrer.</p>
        </div>
        @else
        @foreach($concept->generatedQuestions as $generation)
        <div class="bg-[#242736] border border-[#2e3245] rounded-md p-4 mb-4">
            <div class="flex justify-between items-center mb-3">
                <span class="text-[#8b90a7] text-xs font-mono">
                    Génération du {{ $generation->created_at->format('d/m/Y à H\hi') }}
                </span>
                <form method="POST" action="{{ route('generated-questions.destroy', $generation) }}">
                    @method('DELETE')
                    @csrf
                    <button class="text-[#8b90a7] hover:text-[#ef4444] text-xs font-mono">Supprimer</button>
                </form>
            </div>
            <ol class="space-y-2">
                @foreach($generation->questions as $i => $q)
                <li class="flex gap-3 text-sm">
                    <span class="text-[#6366f1] font-mono font-semibold w-4 shrink-0">{{ $i+1 }}.</span>
                    <span class="text-[#f1f3f9]/90">{{ $q }}</span>
                </li>
                @endforeach
            </ol>
        </div>
        @endforeach
        @endif

        @if($archivedQuestions->isNotEmpty())
        <form method="GET" action="{{ route('domains.concepts.show', [$domain, $concept]) }}" class="mb-4">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="show_archived" value="1" {{ $showArchived ? 'checked' : '' }} onchange="this.form.submit()" class="w-4 h-4 rounded border-[#2e3245] bg-[#242736] text-[#6366f1]">
                <span class="text-sm font-mono text-[#8b90a7]">Afficher les générations archivées</span>
            </label>
        </form>

        @if($showArchived)
        <div class="opacity-75">
            <h3 class="text-sm font-mono font-semibold text-[#8b90a7] mb-3">Générations archivées</h3>
            @foreach($archivedQuestions as $generation)
            <div class="bg-[#242736] border border-[#2e3245] rounded-md p-4 mb-4">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-[#8b90a7] text-xs font-mono">
                        Archivage du {{ $generation->deleted_at->format('d/m/Y à H\hi') }}
                    </span>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('generated-questions.restore', $generation->id) }}">
                            @csrf
                            @method('PATCH')
                            <button class="text-[#22c55e] text-xs font-mono">Restaurer</button>
                        </form>
                        <form method="POST" action="{{ route('generated-questions.forceDelete', $generation->id) }}">
                            @csrf
                            @method('DELETE')
                            <button class="text-[#ef4444] text-xs font-mono">Supprimer définitivement</button>
                        </form>
                    </div>
                </div>
                <ol class="space-y-2">
                    @foreach($generation->questions as $i => $q)
                    <li class="flex gap-3 text-sm">
                        <span class="text-[#6366f1] font-mono font-semibold w-4 shrink-0">{{ $i+1 }}.</span>
                        <span class="text-[#f1f3f9]/90">{{ $q }}</span>
                    </li>
                    @endforeach
                </ol>
            </div>
            @endforeach
        </div>
        @endif
        @endif
    </div>
</div>
@endsection