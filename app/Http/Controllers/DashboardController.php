<?php

namespace App\Http\Controllers;

use App\Models\Concept;
use App\Models\Domain;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $conceptsByStatus = Concept::whereHas('domain', fn($q) => $q->where('user_id', auth()->id()))
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'À revoir' => $conceptsByStatus['to_review'] ?? 0,
            'En cours' => $conceptsByStatus['in_progress'] ?? 0,
            'Maîtrisé' => $conceptsByStatus['mastered'] ?? 0,
        ];

        $domains = Domain::where('user_id', auth()->id())
            ->withCount([
                'concepts',
                'concepts as mastered_count' => fn($q) => $q->where('status', 'mastered'),
                'concepts as to_review_count' => fn($q) => $q->where('status', 'to_review'),
            ])
            ->get();

        $totalDomains = $domains->count();
        $totalConcepts = $domains->sum('concepts_count');

        $domainsWithConcepts = $domains->filter(fn($d) => $d->concepts_count > 0);

        $bestDomain = null;
        if ($domainsWithConcepts->isNotEmpty()) {
            $bestDomain = $domainsWithConcepts->sortByDesc(fn($d) => $d->concepts_count > 0 ? $d->mastered_count / $d->concepts_count : 0)->first();
        }

        $mostToReviewDomain = $domains->sortByDesc('to_review_count')->first();

        return view('dashboard', compact(
            'stats',
            'bestDomain',
            'mostToReviewDomain',
            'totalDomains',
            'totalConcepts'
        ));
    }
}