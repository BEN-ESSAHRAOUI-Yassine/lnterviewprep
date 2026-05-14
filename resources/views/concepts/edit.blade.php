@extends('layouts.app')

@section('title', 'Modifier le concept')

@section('content')
<div class="mb-6">
    <a href="{{ route('domains.concepts.index', $domain) }}" class="text-[#8b90a7] hover:text-[#f1f3f9] text-sm font-mono">← {{ $domain->name }}</a>
</div>

<h1 class="text-2xl font-mono font-semibold text-[#f1f3f9] mb-8">Modifier le concept</h1>

<form method="POST" action="{{ route('domains.concepts.update', [$domain, $concept]) }}" class="max-w-2xl">
    @csrf
    @method('PATCH')

    <div class="mb-6">
        <label class="block text-sm font-mono text-[#8b90a7] mb-2">Titre du concept</label>
        <input type="text" name="title" value="{{ $concept->title }}" required
            class="w-full bg-[#242736] border border-[#2e3245] rounded-md px-4 py-2.5 text-sm font-mono text-[#f1f3f9] focus:border-[#6366f1] focus:outline-none">
        @error('title')
            <p class="text-[#ef4444] text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-6">
        <label class="block text-sm font-mono text-[#8b90a7] mb-2">Explication</label>
        <textarea name="explanation" rows="8" required
            class="w-full bg-[#242736] border border-[#2e3245] rounded-md px-4 py-2.5 text-sm font-sans text-[#f1f3f9] focus:border-[#6366f1] focus:outline-none resize-y min-h-[200px]">{{ $concept->explanation }}</textarea>
        @error('explanation')
            <p class="text-[#ef4444] text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-6">
        <label class="block text-sm font-mono text-[#8b90a7] mb-2">Niveau de difficulté</label>
        <div class="flex gap-4">
            @foreach(['junior' => 'Junior', 'mid' => 'Mid', 'senior' => 'Senior'] as $value => $label)
            <label class="cursor-pointer">
                <input type="radio" name="difficulty" value="{{ $value }}" {{ $concept->difficulty === $value ? 'checked' : '' }} class="sr-only peer">
                <span class="block border border-[#2e3245] rounded-md px-4 py-2 text-sm font-mono text-[#8b90a7] peer-checked:bg-[#6366f1] peer-checked:text-white peer-checked:border-[#6366f1] transition-colors">
                    {{ $label }}
                </span>
            </label>
            @endforeach
        </div>
        @error('difficulty')
            <p class="text-[#ef4444] text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-8">
        <label class="block text-sm font-mono text-[#8b90a7] mb-2">Statut</label>
        <select name="status" class="bg-[#242736] border border-[#2e3245] rounded-md px-4 py-2.5 text-sm font-mono text-[#f1f3f9] focus:border-[#6366f1] focus:outline-none">
            <option value="to_review" {{ $concept->status === 'to_review' ? 'selected' : '' }}>À revoir</option>
            <option value="in_progress" {{ $concept->status === 'in_progress' ? 'selected' : '' }}>En cours</option>
            <option value="mastered" {{ $concept->status === 'mastered' ? 'selected' : '' }}>Maîtrisé</option>
        </select>
        @error('status')
            <p class="text-[#ef4444] text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex gap-4">
        <a href="{{ route('domains.concepts.index', $domain) }}" class="bg-transparent hover:bg-[#242736] border border-[#2e3245] text-[#8b90a7] hover:text-[#f1f3f9] font-mono text-sm px-5 py-2.5 rounded-md transition-colors">Annuler</a>
        <button type="submit" class="bg-[#6366f1] hover:bg-[#4f46e5] text-white font-mono text-sm px-5 py-2.5 rounded-md transition-colors">Enregistrer</button>
    </div>
</form>
@endsection