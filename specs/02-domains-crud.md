# Spec 02 — Domains CRUD

**Feature:** Create, Read, Update, Delete Domains  
**User Stories:** US2, US3, US4  
**Branch:** `feature/domains-crud`  
**Agent:** OpenCode  
**Author:** BEN-ESSAHRAOUI Yassine  

---

## 1. Objective

Build the full CRUD for the `domains` table. Each domain belongs to an authenticated user and groups related technical concepts. The index page must show per-domain progress (total concepts vs mastered concepts) without N+1 queries.

---

## 2. Context & Constraints

- All domain routes are protected by `auth` middleware
- A user can only see, edit, or delete their own domains — scope every query to `auth()->id()`
- No JavaScript — all interactions are standard HTML forms
- The color field is a visual badge — store as a hex string or a named CSS color string

---

## 3. Database

Table: `domains`

| Column | Type | Notes |
|---|---|---|
| id | bigint unsigned | PK, auto-increment |
| user_id | bigint unsigned | FK → users.id, onDelete cascade |
| name | varchar(255) | |
| color | varchar(50) | |
| deleted_at | timestamp | nullable — soft delete |
| created_at | timestamp | |
| updated_at | timestamp | |

---

## 4. Files to Create

| File | Purpose |
|---|---|
| `database/migrations/xxxx_create_domains_table.php` | Domains table with soft deletes |
| `app/Models/Domain.php` | Model with SoftDeletes, fillable, relations |
| `app/Http/Controllers/DomainController.php` | Full CRUD controller |
| `app/Http/Requests/StoreDomainRequest.php` | Validation for create |
| `app/Http/Requests/UpdateDomainRequest.php` | Validation for update |
| `resources/views/domains/index.blade.php` | List of domains with progress |
| `resources/views/domains/create.blade.php` | Create form |
| `resources/views/domains/edit.blade.php` | Edit form |
| `resources/views/domains/show.blade.php` | Domain detail (lists its concepts) |

---

## 5. Files to Modify

| File | Change |
|---|---|
| `routes/web.php` | Add domain resource routes + restore + force-delete |

---

## 6. Routes

```
GET    /domains                         domains.index
GET    /domains/create                  domains.create
POST   /domains                         domains.store
GET    /domains/{domain}                domains.show
GET    /domains/{domain}/edit           domains.edit
PATCH  /domains/{domain}               domains.update
DELETE /domains/{domain}               domains.destroy        (soft delete)
```

Note: restore and force-delete routes are defined in Spec 05.

---

## 7. Model — `Domain.php`

```php
use SoftDeletes;

protected $fillable = ['user_id', 'name', 'color'];

// Relations
public function user(): BelongsTo
public function concepts(): HasMany  // default: excludes soft-deleted

// Soft-delete cascade — override delete()
public function delete(): bool
{
    // soft-delete all concepts, which cascade to their generated questions
    foreach ($this->concepts()->withTrashed()->get() as $concept) {
        $concept->delete(); // triggers Concept::delete() which cascades further
    }
    return parent::delete();
}
```

---

## 8. Controller — `DomainController.php`

### `index()`
```php
$domains = Domain::where('user_id', auth()->id())
    ->withCount([
        'concepts',
        'concepts as mastered_count' => fn($q) => $q->where('status', 'mastered')
    ])
    ->get();
```
→ return `domains.index` with `$domains`

### `create()`
→ return `domains.create`

### `store(StoreDomainRequest $request)`
→ create domain with `user_id = auth()->id()`  
→ redirect to `domains.index` with success message

### `show(Domain $domain)`
→ authorize: `$domain->user_id === auth()->id()` — abort(403) otherwise  
→ load concepts (see Concepts Spec for detail)  
→ return `domains.show`

### `edit(Domain $domain)`
→ authorize ownership  
→ return `domains.edit` with `$domain`

### `update(UpdateDomainRequest $request, Domain $domain)`
→ authorize ownership  
→ update and redirect to `domains.index` with success message

### `destroy(Domain $domain)`
→ authorize ownership  
→ call `$domain->delete()` (triggers cascade soft delete)  
→ redirect to `domains.index` with success message

---

## 9. Form Request Validation

### StoreDomainRequest & UpdateDomainRequest
```
name  → required | string | max:255
color → required | string | max:50
```

Both requests must override `authorize()` to return `true`.

---

## 10. Views

### `domains/index.blade.php`
- Table or card list of all domains
- Each row shows: domain name, color badge, total concepts count, mastered concepts count, progress (e.g. "3 / 7 mastered")
- Buttons: Show, Edit, Delete (DELETE form with CSRF + `@method('DELETE')`)
- Link to create a new domain

### `domains/create.blade.php`
- Form: name (text input), color (text input or color picker `type="color"`)
- Submit → POST `/domains`

### `domains/edit.blade.php`
- Same form pre-filled with domain data
- Submit → PATCH `/domains/{domain}`

### `domains/show.blade.php`
- Domain name + color badge at top
- List of concepts (covered in Spec 03)
- This page is also where the archived-concepts checkbox toggle will live (Spec 05)

---

## 11. What the Agent Must NOT Do

- ❌ Do not use `$request->validate()` in controllers — always use FormRequest classes
- ❌ Do not query domains without scoping to `auth()->id()`
- ❌ Do not use lazy loading on the index page — use `withCount()` with eager loading
- ❌ Do not implement hard delete in `destroy()` — only soft delete
- ❌ Do not define restore/force-delete routes here — those are in Spec 05
- ❌ Do not allow a user to access another user's domain — always check ownership
- ❌ Do not forget `@method('DELETE')` and `@method('PATCH')` in Blade forms
- ❌ Do not forget `@csrf` on every form

---

## 12. Acceptance Criteria

- [ ] Authenticated user sees only their own domains on index
- [ ] Index shows correct concept count and mastered count per domain (no N+1)
- [ ] Creating a domain with valid data saves it and redirects with a success flash
- [ ] Creating a domain with missing name or color shows validation errors
- [ ] Editing a domain updates name and/or color correctly
- [ ] Deleting a domain soft-deletes it (record remains in DB with `deleted_at` set)
- [ ] Deleted domain no longer appears on the index page
- [ ] Accessing another user's domain returns 403
