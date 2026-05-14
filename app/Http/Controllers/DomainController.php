<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDomainRequest;
use App\Http\Requests\UpdateDomainRequest;
use App\Models\Domain;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DomainController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Domain::class);

        $domains = Domain::where('user_id', auth()->id())
            ->withCount([
                'concepts',
                'concepts as mastered_count' => fn($q) => $q->where('status', 'mastered')
            ])
            ->get();

        return view('domains.index', compact('domains'));
    }

    public function create(): View
    {
        $this->authorize('create', Domain::class);

        return view('domains.create');
    }

    public function store(StoreDomainRequest $request): RedirectResponse
    {
        $this->authorize('create', Domain::class);

        Domain::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'color' => $request->color,
        ]);

        return redirect()->route('domains.index')->with('success', 'Domaine créé avec succès.');
    }

    public function show(Domain $domain): View
    {
        $this->authorize('view', $domain);

        $domain->load('concepts.generatedQuestions');

        return view('domains.show', compact('domain'));
    }

    public function edit(Domain $domain): View
    {
        $this->authorize('update', $domain);

        return view('domains.edit', compact('domain'));
    }

    public function update(UpdateDomainRequest $request, Domain $domain): RedirectResponse
    {
        $this->authorize('update', $domain);

        $domain->update($request->validated());

        return redirect()->route('domains.index')->with('success', 'Domaine mis à jour.');
    }

    public function destroy(Domain $domain): RedirectResponse
    {
        $this->authorize('delete', $domain);

        $domain->delete();

        return redirect()->route('domains.index')->with('success', 'Domaine supprimé.');
    }

    public function archived(): View
    {
        $this->authorize('viewAny', Domain::class);

        $domains = Domain::onlyTrashed()
            ->where('user_id', auth()->id())
            ->get();

        return view('domains.archived', compact('domains'));
    }

    public function restore(int $id): RedirectResponse
    {
        $domain = Domain::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $domain);

        $domain->restore();

        return redirect()->route('domains.archived')->with('success', 'Domaine restauré.');
    }

    public function forceDelete(int $id): RedirectResponse
    {
        $domain = Domain::onlyTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $domain);

        $domain->forceDelete();

        return redirect()->route('domains.archived')->with('success', 'Domaine supprimé définitivement.');
    }
}