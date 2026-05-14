@extends('layouts.app')

@section('title', 'Créer un domaine')

@section('content')
<h1 class="text-2xl font-mono font-semibold text-[#f1f3f9] mb-8">Créer un domaine</h1>

<form method="POST" action="{{ route('domains.store') }}" class="max-w-md">
    @csrf

    <div class="mb-6">
        <label class="block text-sm font-mono text-[#8b90a7] mb-2">Nom du domaine</label>
        <input type="text" name="name" value="{{ old('name') }}" required
            class="w-full bg-[#242736] border border-[#2e3245] rounded-md px-4 py-2.5 text-sm font-mono text-[#f1f3f9] focus:border-[#6366f1] focus:outline-none">
        @error('name')
            <p class="text-[#ef4444] text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-8">
        <label class="block text-sm font-mono text-[#8b90a7] mb-2">Couleur du badge</label>
        <div class="flex gap-3">
            @foreach(['#3b82f6', '#22c55e', '#ef4444', '#a855f7', '#f97316', '#eab308', '#ec4899', '#6b7280'] as $color)
            <label class="cursor-pointer">
                <input type="radio" name="color" value="{{ $color }}" {{ old('color') === $color ? 'checked' : '' }} class="sr-only peer">
                <span class="w-8 h-8 rounded-full block ring-2 ring-transparent peer-checked:ring-white peer-checked:ring-offset-2 peer-checked:ring-offset-[#1a1d27]" style="background-color: {{ $color }}"></span>
            </label>
            @endforeach
        </div>
        @error('color')
            <p class="text-[#ef4444] text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex gap-4">
        <a href="{{ route('domains.index') }}" class="bg-transparent hover:bg-[#242736] border border-[#2e3245] text-[#8b90a7] hover:text-[#f1f3f9] font-mono text-sm px-5 py-2.5 rounded-md transition-colors">Annuler</a>
        <button type="submit" class="bg-[#6366f1] hover:bg-[#4f46e5] text-white font-mono text-sm px-5 py-2.5 rounded-md transition-colors">Enregistrer</button>
    </div>
</form>
@endsection