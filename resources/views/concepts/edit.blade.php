@extends('layouts.app')

@section('title', 'Modifier le concept')

@section('content')
<div class="mb-6">
    <a href="{{ route('domains.concepts.index', $domain) }}" class="text-[var(--text-muted)] hover:text-[var(--text-primary)] text-sm font-mono">← {{ $domain->name }}</a>
</div>

<h1 class="text-2xl font-mono font-semibold text-[var(--text-primary)] mb-8">Modifier le concept</h1>

<form method="POST" action="{{ route('domains.concepts.update', [$domain, $concept]) }}" class="max-w-2xl">
    @csrf
    @method('PATCH')

    <div class="mb-6">
        <label class="block text-sm font-mono text-[var(--text-muted)] mb-2">Titre du concept</label>
        <input type="text" name="title" value="{{ $concept->title }}" required
            class="w-full bg-[var(--bg-elevated)] border border-[var(--border)] rounded-md px-4 py-2.5 text-sm font-mono text-[var(--text-primary)] focus:border-[var(--accent)] focus:outline-none">
        @error('title')
            <p class="text-[var(--status-review)] text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-6">
        <label class="block text-sm font-mono text-[var(--text-muted)] mb-2">Explication</label>
        <textarea name="explanation" rows="8" required
            class="w-full bg-[var(--bg-elevated)] border border-[var(--border)] rounded-md px-4 py-2.5 text-sm font-sans text-[var(--text-primary)] focus:border-[var(--accent)] focus:outline-none resize-y min-h-[200px]">{{ $concept->explanation }}</textarea>
        @error('explanation')
            <p class="text-[var(--status-review)] text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-6">
        <label class="block text-sm font-mono text-[var(--text-muted)] mb-2">Niveau de difficulté</label>
        <div class="flex gap-4">
            @foreach(['junior' => 'Junior', 'mid' => 'Mid', 'senior' => 'Senior'] as $value => $label)
            <label class="cursor-pointer">
                <input type="radio" name="difficulty" value="{{ $value }}" {{ $concept->difficulty === $value ? 'checked' : '' }} class="sr-only peer">
                <span class="block border border-[var(--border)] rounded-md px-4 py-2 text-sm font-mono text-[var(--text-muted)] peer-checked:bg-[var(--accent)] peer-checked:text-white peer-checked:border-[var(--accent)] transition-colors">
                    {{ $label }}
                </span>
            </label>
            @endforeach
        </div>
        @error('difficulty')
            <p class="text-[var(--status-review)] text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-8">
        <label class="block text-sm font-mono text-[var(--text-muted)] mb-2">Statut</label>
        <select name="status" class="bg-[var(--bg-elevated)] border border-[var(--border)] rounded-md px-4 py-2.5 text-sm font-mono text-[var(--text-primary)] focus:border-[var(--accent)] focus:outline-none">
            <option value="to_review" {{ $concept->status === 'to_review' ? 'selected' : '' }}>À revoir</option>
            <option value="in_progress" {{ $concept->status === 'in_progress' ? 'selected' : '' }}>En cours</option>
            <option value="mastered" {{ $concept->status === 'mastered' ? 'selected' : '' }}>Maîtrisé</option>
        </select>
        @error('status')
            <p class="text-[var(--status-review)] text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex gap-4">
        <a href="{{ route('domains.concepts.index', $domain) }}" class="bg-transparent hover:bg-[var(--bg-elevated)] border border-[var(--border)] text-[var(--text-muted)] hover:text-[var(--text-primary)] font-mono text-sm px-5 py-2.5 rounded-md transition-colors">Annuler</a>
        <button type="submit" class="bg-[var(--accent)] hover:bg-[var(--accent-soft)] text-white font-mono text-sm px-5 py-2.5 rounded-md transition-colors">Enregistrer</button>
    </div>
</form>
@endsection