# Spec 06 — Dashboard (Bonus)

**Feature:** Progression statistics homepage  
**Branch:** `feature/dashboard`  
**Agent:** OpenCode  
**Author:** BEN-ESSAHRAOUI Yassine  

---

## 1. Objective

Replace the default Laravel welcome page with a personalized dashboard for the authenticated user. The dashboard shows global statistics: number of concepts per status, the best-mastered domain, and the domain that needs the most review — all computed without N+1 queries.

---

## 2. Context & Constraints

- Dashboard is only accessible to authenticated users — protected by `auth` middleware
- All statistics are scoped to the authenticated user's data only
- No JavaScript — pure Blade with server-side computed data
- Redirect `/` to the dashboard if logged in, or to `/login` if guest

---

## 3. Database

No new tables. Reads from `domains`, `concepts` using existing relations and aggregates.

---

## 4. Files to Create

| File | Purpose |
|---|---|
| `app/Http/Controllers/DashboardController.php` | Computes and returns all dashboard statistics |
| `resources/views/dashboard.blade.php` | Dashboard view with stats cards |

---

## 5. Files to Modify

| File | Change |
|---|---|
| `routes/web.php` | Add `GET /` → `DashboardController@index` (auth middleware) |

---

## 6. Route

```
GET / → DashboardController@index    (middleware: auth)
```

If the user is a guest and visits `/`, they should be redirected to `/login` by the `auth` middleware automatically.

---

## 7. Statistics to Compute

### A — Concepts by Status (global)
```php
$conceptsByStatus = Concept::whereHas('domain', fn($q) => $q->where('user_id', auth()->id()))
    ->selectRaw('status, count(*) as total')
    ->groupBy('status')
    ->pluck('total', 'status');

// Result example:
// ['to_review' => 12, 'in_progress' => 5, 'mastered' => 8]
```

Map the keys to human labels for the view:
```php
$stats = [
    'À revoir'  => $conceptsByStatus['to_review']   ?? 0,
    'En cours'  => $conceptsByStatus['in_progress'] ?? 0,
    'Maîtrisé'  => $conceptsByStatus['mastered']    ?? 0,
];
```

### B — Best Mastered Domain
The domain with the highest ratio of mastered concepts to total concepts (minimum 1 concept required).

```php
$domains = Domain::where('user_id', auth()->id())
    ->withCount([
        'concepts',
        'concepts as mastered_count' => fn($q) => $q->where('status', 'mastered'),
    ])
    ->having('concepts_count', '>', 0)
    ->get();

$bestDomain = $domains->sortByDesc(fn($d) => $d->mastered_count / $d->concepts_count)->first();
```

### C — Domain Most In Need of Review
The domain with the highest number of `to_review` concepts.

```php
$mostToReviewDomain = Domain::where('user_id', auth()->id())
    ->withCount([
        'concepts as to_review_count' => fn($q) => $q->where('status', 'to_review'),
    ])
    ->orderByDesc('to_review_count')
    ->first();
```

### D — Total counts (optional, nice to have)
```php
$totalDomains   = Domain::where('user_id', auth()->id())->count();
$totalConcepts  = // sum across user's domains
$totalGenerated = // sum of generated question batches across user's concepts
```

---

## 8. Controller — `DashboardController.php`

```php
public function index()
{
    // Compute A, B, C, D as described above
    return view('dashboard', compact(
        'stats',
        'bestDomain',
        'mostToReviewDomain',
        'totalDomains',
        'totalConcepts'
    ));
}
```

---

## 9. View — `dashboard.blade.php`

The view must display:

### Cards — Concepts by Status
Three stat cards side by side:
- "À revoir" — count
- "En cours" — count  
- "Maîtrisé" — count

### Highlight Cards
- **Best mastered domain:** domain name, color badge, mastered/total ratio (e.g. "5 / 7 mastered")
- **Domain to review most:** domain name, color badge, count of `to_review` concepts

### Summary Numbers
- Total domains
- Total concepts

### Empty State
If the user has no domains yet, show a friendly message and a link to create the first domain.

---

## 10. What the Agent Must NOT Do

- ❌ Do not use lazy loading for statistics — use `withCount()` and aggregate queries
- ❌ Do not show another user's data — all queries must be scoped to `auth()->id()`
- ❌ Do not crash if the user has no domains (handle empty collections gracefully)
- ❌ Do not divide by zero when computing mastery ratio — check `concepts_count > 0`
- ❌ Do not add JavaScript to the dashboard
- ❌ Do not leave the default Laravel welcome page at `/`

---

## 11. Acceptance Criteria

- [ ] Guest visiting `/` is redirected to `/login`
- [ ] Logged-in user sees their own statistics only
- [ ] Correct counts for each status (to_review / in_progress / mastered)
- [ ] Best mastered domain is correctly identified by mastery ratio
- [ ] Most-to-review domain is correctly identified by to_review count
- [ ] No N+1 queries on the dashboard (verified with Debugbar)
- [ ] Empty state shown cleanly when user has no domains
- [ ] All numbers are scoped to the authenticated user
