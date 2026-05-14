<?php

namespace App\Http\Controllers;

use App\Models\Concept;
use App\Models\GeneratedQuestion;
use App\Services\GroqService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GeneratedQuestionController extends Controller
{
    public function store(Request $request, Concept $concept)
    {
        $concept->load('domain');
        $tempQuestion = new GeneratedQuestion(['concept_id' => $concept->id]);
        $tempQuestion->setRelation('concept', $concept);
        $this->authorize('create', $tempQuestion);

        $groqService = new GroqService();

        try {
            $questions = $groqService->generateInterviewQuestions(
                $concept->title,
                $concept->explanation,
                $concept->difficultyLabel,
                $concept->statusLabel
            );

            GeneratedQuestion::create([
                'concept_id' => $concept->id,
                'questions' => $questions,
            ]);

            return back()->with('success', '5 questions générées avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(GeneratedQuestion $generatedQuestion): RedirectResponse
    {
        $generatedQuestion->load('concept.domain');
        $this->authorize('delete', $generatedQuestion);

        $generatedQuestion->delete();

        return back()->with('success', 'Génération supprimée.');
    }

    public function restore(int $id): RedirectResponse
    {
        $question = GeneratedQuestion::onlyTrashed()->findOrFail($id);
        $question->load('concept.domain');
        $this->authorize('restore', $question);

        $question->restore();

        return back()->with('success', 'Génération restaurée.');
    }

    public function forceDelete(int $id): RedirectResponse
    {
        $question = GeneratedQuestion::onlyTrashed()->findOrFail($id);
        $question->load('concept.domain');
        $this->authorize('forceDelete', $question);

        $question->forceDelete();

        return back()->with('success', 'Génération supprimée définitivement.');
    }
}