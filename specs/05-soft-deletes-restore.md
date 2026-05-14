# Spec 05 — Soft Deletes, Restore & Force Delete

**Feature:** Archive, restore, and permanently delete Domains, Concepts, and GeneratedQuestions  
**Bonus User Story:** Soft deletes on all three models with restore pages  
**Branch:** `feature/soft-deletes`  
**Agent:** OpenCode  
**Author:** BEN-ESSAHRAOUI Yassine  

---

## 1. Objective

All three models (Domain, Concept, GeneratedQuestion) use Laravel's `SoftDeletes` trait. Deleting any record sets `deleted_at` — the record is hidden from normal queries but preserved in the database. Users can restore a soft-deleted record back to active, or permanently remove it with force delete.

Cascade behavior:
- Soft-deleting a Domain → soft-deletes all its Concepts → soft-deletes all their GeneratedQuestions
- Soft-deleting a Concept → soft-deletes all its GeneratedQuestions
- Restoring a Domain → does NOT auto-restore its Concepts or GeneratedQuestions (user restores them individually)
- Restoring a Concept → does NOT auto-restore its GeneratedQuestions
- Force-deleting a Domain → permanently deletes the Domain row only (cascades handled by DB FK if needed, or manually)

---

## 2. Context & Constraints

- All three models already have `deleted_at` column (defined in their respective migrations)
- All three models already use the `SoftDeletes` trait (defined in previous specs)
- No JavaScript — all restore/force-delete actions are standard HTML forms
- Ownership check applies to all archived pages and actions

---

## 3. Archived UI Pattern per Model

### Domain — Dedicated Archived Page
- URL: `GET /domains/archived`
- Shows all soft-deleted domains belonging to the authenticated user
- Each row has two buttons: **Restore** (PATCH) and **Force Delete** (DELETE)

### Concept — Checkbox Toggle Inside Domain Detail Page
- URL: `GET /domains/{domain}` (the domain show/detail page)
- A checkbox labeled "Show archived concepts"
- When checked, submits a GET form appending `?show_archived=1` to the URL
- The controller checks for this query param and includes `withTrashed()` + filters to only show soft-deleted ones when the checkbox is active, OR shows both active and archived, depending on implementation choice below
- **Implementation choice:** When `?show_archived=1` is present, show archived concepts in a separate section below the active list — each archived concept has **Restore** and **Force Delete** buttons

### GeneratedQuestion — Checkbox Toggle Inside Concept Detail Page
- URL: `GET /domains/{domain}/concepts/{concept}` (the concept show page)
- Same pattern as concepts: a checkbox labeled "Show archived questions"
- When checked, appends `?show_archived=1` to the concept show URL
- Archived questions shown in a separate section below the active generation history
- Each archived generation has **Restore** and **Force Delete** buttons

---

## 4. Routes to Add

These routes complement the CRUD routes defined in Specs 02, 03, and 04.

```
GET    /domains/archived                                    domains.archived
PATCH  /domains/{domain}/restore                           domains.restore
DELETE /domains/{domain}/force-delete                      domains.forceDelete

PATCH  /domains/{domain}/concepts/{concept}/restore        concepts.restore
DELETE /domains/{domain}/concepts/{concept}/force-delete   concepts.forceDelete

PATCH  /generated-questions/{generatedQuestion}/restore    generated-questions.restore
DELETE /generated-questions/{generatedQuestion}/force-delete  generated-questions.forceDelete
```

**Important:** The route `domains/archived` must be defined BEFORE `domains/{domain}` in `routes/web.php` to avoid Laravel matching "archived" as a domain ID.

---

## 5. Files to Modify

| File | Change |
|---|---|
| `app/Http/Controllers/DomainController.php` | Add `archived()`, `restore()`, `forceDelete()` methods |
| `app/Http/Controllers/ConceptController.php` | Add `restore()`, `forceDelete()` methods; update `show()` for archived toggle |
| `app/Http/Controllers/GeneratedQuestionController.php` | Add `restore()`, `forceDelete()` methods; update concept show pass-through |
| `resources/views/domains/archived.blade.php` | New view — archived domains list |
| `resources/views/domains/show.blade.php` | Add archived concepts section with checkbox |
| `resources/views/concepts/show.blade.php` | Add archived questions section with checkbox |
| `routes/web.php` | Add all restore and force-delete routes listed above |

---

## 6. Controller Methods

### DomainController

#### `archived()`
```php
$domains = Domain::onlyTrashed()
    ->where('user_id', auth()->id())
    ->get();

return view('domains.archived', compact('domains'));
```

#### `restore(int $id)`
```php
$domain = Domain::onlyTrashed()->where('user_id', auth()->id())->findOrFail($id);
$domain->restore(); // restores only the domain row — not its concepts
redirect()->route('domains.archived')->with('success', 'Domain restored.');
```

#### `forceDelete(int $id)`
```php
$domain = Domain::onlyTrashed()->where('user_id', auth()->id())->findOrFail($id);
$domain->forceDelete();
redirect()->route('domains.archived')->with('success', 'Domain permanently deleted.');
```

---

### ConceptController

#### Update `show(Domain $domain, Concept $concept)` to handle archived toggle:
```php
$showArchived = $request->boolean('show_archived');

// Active generated questions (already eager-loaded)
$activeQuestions = $concept->generatedQuestions;

// Archived generated questions (only if checkbox is checked)
$archivedQuestions = $showArchived
    ? $concept->generatedQuestions()->onlyTrashed()->get()
    : collect();

return view('concepts.show', compact('concept', 'domain', 'activeQuestions', 'archivedQuestions', 'showArchived'));
```

#### Update `show(Domain $domain)` (domain detail) to handle archived concepts toggle:
This is actually handled in `DomainController@show`:
```php
$showArchived = $request->boolean('show_archived');

$activeConcepts = $domain->concepts()->get();

$archivedConcepts = $showArchived
    ? $domain->concepts()->onlyTrashed()->get()
    : collect();

return view('domains.show', compact('domain', 'activeConcepts', 'archivedConcepts', 'showArchived'));
```

#### `restore(Domain $domain, int $conceptId)`
```php
$concept = Concept::onlyTrashed()->where('domain_id', $domain->id)->findOrFail($conceptId);
// authorize domain ownership
$concept->restore();
redirect()->back()->with('success', 'Concept restored.');
```

#### `forceDelete(Domain $domain, int $conceptId)`
```php
$concept = Concept::onlyTrashed()->where('domain_id', $domain->id)->findOrFail($conceptId);
// authorize domain ownership
$concept->forceDelete();
redirect()->back()->with('success', 'Concept permanently deleted.');
```

---

### GeneratedQuestionController

#### `restore(int $id)`
```php
$question = GeneratedQuestion::onlyTrashed()->findOrFail($id);
// authorize via $question->concept->domain->user_id
$question->restore();
redirect()->back()->with('success', 'Generation restored.');
```

#### `forceDelete(int $id)`
```php
$question = GeneratedQuestion::onlyTrashed()->findOrFail($id);
// authorize via $question->concept->domain->user_id
$question->forceDelete();
redirect()->back()->with('success', 'Generation permanently deleted.');
```

---

## 7. Views

### `domains/archived.blade.php`
- Page title: "Archived Domains"
- Link back to active domains index
- Table of archived domains showing: name, color badge, deleted date
- Per row:
  - **Restore** button → PATCH `/domains/{id}/restore`
  - **Force Delete** button → DELETE `/domains/{id}/force-delete` with a confirmation note
- Empty state if no archived domains

### `domains/show.blade.php` — Archived Concepts Section
Add below the active concepts list:

```blade
{{-- Archived concepts toggle --}}
<form method="GET" action="{{ route('domains.show', $domain) }}">
    <label>
        <input type="checkbox"
               name="show_archived"
               value="1"
               {{ $showArchived ? 'checked' : '' }}
               onchange="this.form.submit()">
        Show archived concepts
    </label>
    {{-- Preserve other active filters if any --}}
</form>

@if($showArchived && $archivedConcepts->isNotEmpty())
    <h3>Archived Concepts</h3>
    @foreach($archivedConcepts as $concept)
        <div>
            <span>{{ $concept->title }}</span>
            <span>{{ $concept->difficulty_label }}</span>

            {{-- Restore --}}
            <form method="POST" action="{{ route('concepts.restore', [$domain, $concept->id]) }}">
                @csrf @method('PATCH')
                <button type="submit">Restore</button>
            </form>

            {{-- Force Delete --}}
            <form method="POST" action="{{ route('concepts.forceDelete', [$domain, $concept->id]) }}">
                @csrf @method('DELETE')
                <button type="submit">Delete permanently</button>
            </form>
        </div>
    @endforeach
@endif
```

Note: the `onchange="this.form.submit()"` is the only inline JavaScript allowed — it is a single attribute on a form input for progressive enhancement. If the agent refuses, a visible submit button is acceptable.

### `concepts/show.blade.php` — Archived Questions Section
Same pattern as archived concepts, but for `$archivedQuestions` passed from the controller:

```blade
{{-- Archived questions toggle --}}
<form method="GET" action="{{ route('concepts.show', [$domain, $concept]) }}">
    <label>
        <input type="checkbox"
               name="show_archived"
               value="1"
               {{ $showArchived ? 'checked' : '' }}
               onchange="this.form.submit()">
        Show archived questions
    </label>
</form>

@if($showArchived && $archivedQuestions->isNotEmpty())
    <h3>Archived Generations</h3>
    @foreach($archivedQuestions as $generation)
        <div>
            <small>Archived on {{ $generation->deleted_at->format('d/m/Y H:i') }}</small>
            <ol>
                @foreach($generation->questions as $question)
                    <li>{{ $question }}</li>
                @endforeach
            </ol>

            {{-- Restore --}}
            <form method="POST" action="{{ route('generated-questions.restore', $generation->id) }}">
                @csrf @method('PATCH')
                <button type="submit">Restore</button>
            </form>

            {{-- Force Delete --}}
            <form method="POST" action="{{ route('generated-questions.forceDelete', $generation->id) }}">
                @csrf @method('DELETE')
                <button type="submit">Delete permanently</button>
            </form>
        </div>
    @endforeach
@endif
```

---

## 8. Cascade Soft Delete — Model Override Summary

These overrides must already exist from Specs 02 and 03. Listed here for completeness:

### `Domain::delete()`
```php
public function delete(): bool
{
    foreach ($this->concepts()->get() as $concept) {
        $concept->delete(); // triggers Concept::delete() cascade
    }
    return parent::delete();
}
```

### `Concept::delete()`
```php
public function delete(): bool
{
    foreach ($this->generatedQuestions()->get() as $question) {
        $question->delete();
    }
    return parent::delete();
}
```

**Important:** These overrides use `->get()` not `->withTrashed()->get()` — we only cascade soft-delete currently active records. Already-archived children stay archived.

---

## 9. What the Agent Must NOT Do

- ❌ Do not use DB-level `ON DELETE CASCADE` to handle soft-delete cascades — it won't work with soft deletes
- ❌ Do not define the `domains/archived` route after `domains/{domain}` — it will be swallowed by the dynamic segment
- ❌ Do not auto-restore children when restoring a parent — restore is always individual
- ❌ Do not skip ownership verification on restore and force-delete actions
- ❌ Do not use `withTrashed()` in normal (non-archived) queries — it breaks the default scoping
- ❌ Do not flash a confirmation modal using JavaScript — a simple HTML `<button>` is sufficient
- ❌ Do not forget `@method('PATCH')` and `@method('DELETE')` on restore and force-delete forms

---

## 10. Acceptance Criteria

- [ ] Soft-deleting a domain hides it from the index and its concepts from the domain show page
- [ ] Soft-deleting a domain cascades to soft-delete all its concepts and their generated questions
- [ ] Soft-deleting a concept cascades to soft-delete all its generated questions
- [ ] Archived domains page shows all soft-deleted domains for the current user
- [ ] Restoring a domain makes it reappear on the domains index
- [ ] Force-deleting a domain permanently removes it from the database
- [ ] Archived concepts appear in the domain show page only when the checkbox is checked
- [ ] Restoring a concept makes it reappear in the domain's active concept list
- [ ] Archived questions appear in the concept show page only when the checkbox is checked
- [ ] Restoring a question makes it reappear in the active generation history
- [ ] Force-deleting any record permanently removes it from the database
- [ ] A user cannot restore or force-delete another user's records (403)
