<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConceptRequest;
use App\Http\Requests\UpdateConceptRequest;
use App\Models\Concept;
use App\Models\Domain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConceptController extends Controller
{
    public function index(Domain $domain): View
    {
        $this->authorize('view', $domain);

        $query = $domain->concepts();

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        if (request()->filled('difficulty')) {
            $query->where('difficulty', request('difficulty'));
        }

        $concepts = $query->with('generatedQuestions')->get();
        $showArchived = request()->boolean('show_archived');
        $archivedConcepts = $domain->concepts()->onlyTrashed()->get();

        return view('concepts.index', compact('domain', 'concepts', 'archivedConcepts', 'showArchived'));
    }

    public function create(Domain $domain): View
    {
        $this->authorize('view', $domain);

        return view('concepts.create', compact('domain'));
    }

    public function store(StoreConceptRequest $request, Domain $domain): RedirectResponse
    {
        $this->authorize('view', $domain);

        $domain->concepts()->create([
            'title' => $request->title,
            'explanation' => $request->explanation,
            'difficulty' => $request->difficulty,
            'status' => 'to_review',
        ]);

        return redirect()->route('domains.concepts.index', $domain)->with('success', 'Concept créé avec succès.');
    }

    public function show(Domain $domain, Concept $concept): View
    {
        $this->authorize('view', $concept);
        abort_if($concept->domain_id !== $domain->id, 404);

        $concept->load('generatedQuestions');

        $showArchived = request()->boolean('show_archived');
        $archivedQuestions = $concept->generatedQuestions()->onlyTrashed()->get();

        return view('concepts.show', compact('domain', 'concept', 'archivedQuestions', 'showArchived'));
    }

    public function edit(Domain $domain, Concept $concept): View
    {
        $this->authorize('update', $concept);
        abort_if($concept->domain_id !== $domain->id, 404);

        return view('concepts.edit', compact('domain', 'concept'));
    }

    public function update(UpdateConceptRequest $request, Domain $domain, Concept $concept): RedirectResponse
    {
        $this->authorize('update', $concept);
        abort_if($concept->domain_id !== $domain->id, 404);

        $concept->update($request->validated());

        return redirect()->route('domains.concepts.index', $domain)->with('success', 'Concept mis à jour.');
    }

    public function updateStatus(Request $request, Domain $domain, Concept $concept): RedirectResponse
    {
        $this->authorize('update', $concept);
        abort_if($concept->domain_id !== $domain->id, 404);

        $request->validate([
            'status' => 'required|in:to_review,in_progress,mastered',
        ]);

        $concept->update(['status' => $request->status]);

        return back()->with('success', 'Statut mis à jour.');
    }

    public function destroy(Domain $domain, Concept $concept): RedirectResponse
    {
        $this->authorize('delete', $concept);
        abort_if($concept->domain_id !== $domain->id, 404);

        $concept->delete();

        return redirect()->route('domains.concepts.index', $domain)->with('success', 'Concept supprimé.');
    }

    public function restore(Domain $domain, int $conceptId): RedirectResponse
    {
        $this->authorize('view', $domain);

        $concept = Concept::onlyTrashed()->where('domain_id', $domain->id)->findOrFail($conceptId);
        $this->authorize('restore', $concept);

        $concept->restore();

        return back()->with('success', 'Concept restauré.');
    }

    public function forceDelete(Domain $domain, int $conceptId): RedirectResponse
    {
        $this->authorize('view', $domain);

        $concept = Concept::onlyTrashed()->where('domain_id', $domain->id)->findOrFail($conceptId);
        $this->authorize('forceDelete', $concept);

        $concept->forceDelete();

        return back()->with('success', 'Concept supprimé définitivement.');
    }
}