# Spec 03 — Concepts CRUD

**Feature:** Create, Read, Update, Delete, Filter, and Quick-Status Concepts  
**User Stories:** US5, US6, US7, US8, US9, US10  
**Branch:** `feature/concepts-crud`  
**Agent:** OpenCode  
**Author:** BEN-ESSAHRAOUI Yassine  

---

## 1. Objective

Build the full CRUD for the `concepts` table, nested under domains. Each concept belongs to a domain and tracks a technical topic with a title, an explanation, a difficulty level, and a mastery status. The list page supports filtering by status and by difficulty. A quick-status toggle allows updating the status directly from the list without opening the edit form.

---

## 2. Context & Constraints

- All concept routes are protected by `auth` middleware
- A concept is always accessed through its parent domain — routes are nested: `/domains/{domain}/concepts/...`
- Ownership check: verify `$domain->user_id === auth()->id()` on every action
- No JavaScript — all filters and status changes use standard HTML form POST/GET
- Accessors `statusLabel` and `difficultyLabel` must be defined on the model and used in views

---

## 3. Database

Table: `concepts`

| Column | Type | Notes |
|---|---|---|
| id | bigint unsigned | PK, auto-increment |
| domain_id | bigint unsigned | FK → domains.id, onDelete cascade |
| title | varchar(255) | |
| explanation | text | |
| difficulty | enum('junior','mid','senior') | |
| status | enum('to_review','in_progress','mastered') | default: to_review |
| deleted_at | timestamp | nullable — soft delete |
| created_at | timestamp | |
| updated_at | timestamp | |

---

## 4. Files to Create

| File | Purpose |
|---|---|
| `database/migrations/xxxx_create_concepts_table.php` | Concepts table with soft deletes |
| `app/Models/Concept.php` | Model with SoftDeletes, fillable, accessors, relations |
| `app/Http/Controllers/ConceptController.php` | Full CRUD + quick status update |
| `app/Http/Requests/StoreConceptRequest.php` | Validation for create |
| `app/Http/Requests/UpdateConceptRequest.php` | Validation for update |
| `resources/views/concepts/index.blade.php` | Filtered list of concepts within a domain |
| `resources/views/concepts/create.blade.php` | Create form |
| `resources/views/concepts/edit.blade.php` | Edit form |
| `resources/views/concepts/show.blade.php` | Concept detail with generated questions |

---

## 5. Files to Modify

| File | Change |
|---|---|
| `routes/web.php` | Add concept nested routes under domains |
| `app/Models/Domain.php` | Confirm `concepts()` HasMany relation exists |

---

## 6. Routes

```
GET    /domains/{domain}/concepts                          concepts.index
GET    /domains/{domain}/concepts/create                   concepts.create
POST   /domains/{domain}/concepts                          concepts.store
GET    /domains/{domain}/concepts/{concept}                concepts.show
GET    /domains/{domain}/concepts/{concept}/edit           concepts.edit
PATCH  /domains/{domain}/concepts/{concept}               concepts.update
PATCH  /domains/{domain}/concepts/{concept}/status        concepts.updateStatus
DELETE /domains/{domain}/concepts/{concept}               concepts.destroy   (soft delete)
```

Note: restore and force-delete routes are defined in Spec 05.

---

## 7. Model — `Concept.php`

```php
use SoftDeletes;

protected $fillable = ['domain_id', 'title', 'explanation', 'difficulty', 'status'];

protected $attributes = [
    'status' => 'to_review',
];

// Accessors
public function getStatusLabelAttribute(): string
{
    return match($this->status) {
        'to_review'   => 'À revoir',
        'in_progress' => 'En cours',
        'mastered'    => 'Maîtrisé',
        default       => $this->status,
    };
}

public function getDifficultyLabelAttribute(): string
{
    return match($this->difficulty) {
        'junior' => 'Junior',
        'mid'    => 'Mid',
        'senior' => 'Senior',
        default  => $this->difficulty,
    };
}

// Relations
public function domain(): BelongsTo
public function generatedQuestions(): HasMany

// Soft-delete cascade
public function delete(): bool
{
    foreach ($this->generatedQuestions()->withTrashed()->get() as $question) {
        $question->delete();
    }
    return parent::delete();
}
```

---

## 8. Controller — `ConceptController.php`

### Helper (private)
```php
private function authorizedDomain(int $domainId): Domain
{
    $domain = Domain::findOrFail($domainId);
    if ($domain->user_id !== auth()->id()) abort(403);
    return $domain;
}
```

### `index(Domain $domain)`
- Authorize domain ownership
- Accept optional query params: `?status=` and `?difficulty=`
- Filter `$domain->concepts()` by status and/or difficulty if provided
- Eager-load `generatedQuestions` count
- Return `concepts.index` with `$domain`, `$concepts`, current filter values

### `create(Domain $domain)`
- Authorize ownership
- Return `concepts.create` with `$domain`

### `store(StoreConceptRequest $request, Domain $domain)`
- Authorize ownership
- Create concept with `domain_id = $domain->id`, status defaults to `to_review`
- Redirect to `concepts.index` for that domain with success flash

### `show(Domain $domain, Concept $concept)`
- Authorize domain ownership
- Verify `$concept->domain_id === $domain->id` — abort(404) otherwise
- Eager-load `$concept->generatedQuestions` (non-archived)
- Return `concepts.show`

### `edit(Domain $domain, Concept $concept)`
- Authorize ownership and parent match
- Return `concepts.edit` with `$domain`, `$concept`

### `update(UpdateConceptRequest $request, Domain $domain, Concept $concept)`
- Authorize and verify parent
- Update and redirect to `concepts.index` with success flash

### `updateStatus(Request $request, Domain $domain, Concept $concept)`
- Authorize and verify parent
- Validate: `status` must be in `['to_review', 'in_progress', 'mastered']`
- Update only the `status` field
- Redirect back to `concepts.index`

### `destroy(Domain $domain, Concept $concept)`
- Authorize and verify parent
- Call `$concept->delete()` (triggers cascade soft delete on GeneratedQuestions)
- Redirect to `concepts.index` with success flash

---

## 9. Form Request Validation

### StoreConceptRequest
```
title       → required | string | max:255
explanation → required | string
difficulty  → required | in:junior,mid,senior
status      → sometimes | in:to_review,in_progress,mastered
```

### UpdateConceptRequest
```
title       → required | string | max:255
explanation → required | string
difficulty  → required | in:junior,mid,senior
status      → required | in:to_review,in_progress,mastered
```

---

## 10. Views

### `concepts/index.blade.php`
- Domain name as page heading
- Filter bar (GET form): dropdown for status filter, dropdown for difficulty filter, submit button
- Table or card list of concepts showing:
  - Title
  - Difficulty badge using `$concept->difficulty_label`
  - Status badge using `$concept->status_label`
  - Quick status change: small POST form with a `<select>` for status and a submit button — no page reload beyond standard redirect
  - Links: Show, Edit
  - Delete button (DELETE form with CSRF + `@method('DELETE')`)
- Link to create a new concept

### `concepts/create.blade.php`
- Fields: title (text), explanation (textarea), difficulty (select: junior/mid/senior)
- Status not shown on create — defaults to `to_review` in the model
- Submit → POST `/domains/{domain}/concepts`

### `concepts/edit.blade.php`
- Same fields as create, pre-filled
- Status field included as a select (to_review / in_progress / mastered) — shown with labels
- Submit → PATCH `/domains/{domain}/concepts/{concept}`

### `concepts/show.blade.php`
- Title, difficulty label, status label, full explanation
- Section for generated questions (covered in Spec 04)
- Archived questions checkbox toggle section (covered in Spec 05)
- Edit button, Back button

---

## 11. Combined Filter Logic

The controller must support both filters simultaneously:

```php
$query = $domain->concepts();

if ($request->filled('status')) {
    $query->where('status', $request->status);
}

if ($request->filled('difficulty')) {
    $query->where('difficulty', $request->difficulty);
}

$concepts = $query->get();
```

The Blade form must preserve both filter values when submitting one — use hidden inputs or keep both selects in the same form.

---

## 12. What the Agent Must NOT Do

- ❌ Do not use `$request->validate()` in controllers — always FormRequest classes
- ❌ Do not forget to verify `$concept->domain_id === $domain->id` (route model binding alone is not enough)
- ❌ Do not use `status` as a required field on create — it must default to `to_review` automatically
- ❌ Do not use JavaScript for the quick-status toggle — plain POST form only
- ❌ Do not display raw enum values in views — always use `status_label` and `difficulty_label` accessors
- ❌ Do not implement hard delete in `destroy()` — only soft delete
- ❌ Do not query concepts without scoping through the parent domain
- ❌ Do not forget `@method('DELETE')`, `@method('PATCH')`, and `@csrf` on every form

---

## 13. Acceptance Criteria

- [ ] Concept list shows all concepts for the domain with correct labels (not raw enum values)
- [ ] Filtering by status alone works correctly
- [ ] Filtering by difficulty alone works correctly
- [ ] Filtering by both status and difficulty simultaneously works
- [ ] Creating a concept with valid data saves it with status `to_review` by default
- [ ] Editing a concept updates all fields including status
- [ ] Quick-status toggle from the list updates status without going to the edit form
- [ ] Deleting a concept soft-deletes it and its generated questions
- [ ] Accessing a concept from the wrong domain returns 404
- [ ] Accessing another user's domain returns 403
