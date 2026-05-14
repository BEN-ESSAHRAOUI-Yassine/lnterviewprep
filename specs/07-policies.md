# Spec 07 — Authorization Policies (Ownership)

**Feature:** Laravel Policy classes for Domain, Concept, and GeneratedQuestion  
**Branch:** `feature/policies`  
**Agent:** OpenCode  
**Author:** BEN-ESSAHRAOUI Yassine  

---

## 1. Objective

Replace all manual `if ($domain->user_id !== auth()->id()) abort(403)` checks in controllers with proper Laravel Policy classes. Each model gets its own Policy. Controllers use `$this->authorize()`. The ownership chain is: User → Domain → Concept → GeneratedQuestion.

---

## 2. Context & Constraints

- Laravel 13 — policies are auto-discovered if named correctly (`ModelPolicy` convention)
- No extra package needed — Laravel's Gate and Policy system is built-in
- All three policies enforce that the authenticated user owns the root Domain
- Concept and GeneratedQuestion ownership is always verified by traversing up to the Domain's `user_id`
- Guest users are rejected before policies run — the `auth` middleware handles that

---

## 3. Files to Create

| File | Purpose |
|---|---|
| `app/Policies/DomainPolicy.php` | Ownership rules for Domain actions |
| `app/Policies/ConceptPolicy.php` | Ownership rules for Concept actions (via domain) |
| `app/Policies/GeneratedQuestionPolicy.php` | Ownership rules for GeneratedQuestion actions (via concept → domain) |

---

## 4. Files to Modify

| File | Change |
|---|---|
| `app/Http/Controllers/DomainController.php` | Replace manual abort(403) with `$this->authorize()` |
| `app/Http/Controllers/ConceptController.php` | Replace manual abort(403) with `$this->authorize()` |
| `app/Http/Controllers/GeneratedQuestionController.php` | Replace manual abort(403) with `$this->authorize()` |

No changes needed to `AuthServiceProvider` — Laravel 13 auto-discovers policies following the `ModelPolicy` naming convention.

---

## 5. Policy — `DomainPolicy.php`

```php
namespace App\Policies;

use App\Models\Domain;
use App\Models\User;

class DomainPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // any authenticated user can list their own domains
    }

    public function view(User $user, Domain $domain): bool
    {
        return $user->id === $domain->user_id;
    }

    public function create(User $user): bool
    {
        return true; // any authenticated user can create a domain
    }

    public function update(User $user, Domain $domain): bool
    {
        return $user->id === $domain->user_id;
    }

    public function delete(User $user, Domain $domain): bool
    {
        return $user->id === $domain->user_id;
    }

    public function restore(User $user, Domain $domain): bool
    {
        return $user->id === $domain->user_id;
    }

    public function forceDelete(User $user, Domain $domain): bool
    {
        return $user->id === $domain->user_id;
    }
}
```

---

## 6. Policy — `ConceptPolicy.php`

Concept ownership is determined by its parent Domain's `user_id`. The policy always loads the domain relation to check.

```php
namespace App\Policies;

use App\Models\Concept;
use App\Models\User;

class ConceptPolicy
{
    public function view(User $user, Concept $concept): bool
    {
        return $user->id === $concept->domain->user_id;
    }

    public function create(User $user, Concept $concept): bool
    {
        // $concept is an unsaved instance carrying domain_id — check via domain
        return $user->id === $concept->domain->user_id;
    }

    public function update(User $user, Concept $concept): bool
    {
        return $user->id === $concept->domain->user_id;
    }

    public function delete(User $user, Concept $concept): bool
    {
        return $user->id === $concept->domain->user_id;
    }

    public function restore(User $user, Concept $concept): bool
    {
        return $user->id === $concept->domain->user_id;
    }

    public function forceDelete(User $user, Concept $concept): bool
    {
        return $user->id === $concept->domain->user_id;
    }
}
```

**Important:** The `domain` relation must be eager-loaded before the policy runs. In controllers that receive a `Concept` via route model binding, call `$concept->load('domain')` if not already loaded, or use `$concept->domain` (which Eloquent lazy-loads once — acceptable here since it is a single record, not a list).

---

## 7. Policy — `GeneratedQuestionPolicy.php`

GeneratedQuestion ownership is determined by traversing: GeneratedQuestion → Concept → Domain → user_id.

```php
namespace App\Policies;

use App\Models\GeneratedQuestion;
use App\Models\User;

class GeneratedQuestionPolicy
{
    public function create(User $user, GeneratedQuestion $generatedQuestion): bool
    {
        return $user->id === $generatedQuestion->concept->domain->user_id;
    }

    public function delete(User $user, GeneratedQuestion $generatedQuestion): bool
    {
        return $user->id === $generatedQuestion->concept->domain->user_id;
    }

    public function restore(User $user, GeneratedQuestion $generatedQuestion): bool
    {
        return $user->id === $generatedQuestion->concept->domain->user_id;
    }

    public function forceDelete(User $user, GeneratedQuestion $generatedQuestion): bool
    {
        return $user->id === $generatedQuestion->concept->domain->user_id;
    }
}
```

**Important:** Always eager-load the full chain before policy evaluation:
```php
$question->load('concept.domain');
```

---

## 8. Controller Updates

### DomainController

Replace every manual ownership check with the corresponding `authorize()` call:

| Old (remove) | New (use instead) |
|---|---|
| `if ($domain->user_id !== auth()->id()) abort(403);` | `$this->authorize('view', $domain);` |
| *(same for edit/update)* | `$this->authorize('update', $domain);` |
| *(same for destroy)* | `$this->authorize('delete', $domain);` |
| *(same for restore)* | `$this->authorize('restore', $domain);` |
| *(same for forceDelete)* | `$this->authorize('forceDelete', $domain);` |

For `index()` and `create()` — no model instance needed:
```php
$this->authorize('viewAny', Domain::class);
$this->authorize('create', Domain::class);
```

The `index()` query still scopes to `auth()->id()` for filtering — the policy is an additional gate, not a replacement for the query scope:
```php
$domains = Domain::where('user_id', auth()->id())->withCount([...])->get();
```

---

### ConceptController

For actions that receive both `$domain` and `$concept` via route model binding, authorize against the concept (which internally checks via domain):

```php
// index / create — authorize against the domain
$this->authorize('view', $domain);

// show, edit, update, destroy, updateStatus, restore, forceDelete
$this->authorize('view', $concept);      // or 'update', 'delete', etc.
```

Also keep the parent check to prevent cross-domain URL manipulation:
```php
// After route model binding, still verify the concept belongs to this domain
abort_if($concept->domain_id !== $domain->id, 404);
```

This is not redundant — the policy checks user ownership, but does not verify the URL domain/concept pairing. Both checks are needed.

---

### GeneratedQuestionController

```php
// store — build an unsaved instance to pass to the policy
$generatedQuestion = new GeneratedQuestion(['concept_id' => $concept->id]);
$generatedQuestion->setRelation('concept', $concept->load('domain'));
$this->authorize('create', $generatedQuestion);

// destroy
$generatedQuestion->load('concept.domain');
$this->authorize('delete', $generatedQuestion);

// restore
$generatedQuestion->load('concept.domain');
$this->authorize('restore', $generatedQuestion);

// forceDelete
$generatedQuestion->load('concept.domain');
$this->authorize('forceDelete', $generatedQuestion);
```

---

## 9. Relation Eager Loading Requirements

To avoid both N+1 queries and policy failures, these relations must be loaded before any policy call:

| Controller | Required load before authorize |
|---|---|
| `ConceptController` | `$concept->domain` (single access — Eloquent lazy-load acceptable) |
| `GeneratedQuestionController` | `$question->load('concept.domain')` — always explicit |

Do NOT rely on Eloquent lazy-loading inside policy classes themselves — load before calling `authorize()`.

---

## 10. What the Agent Must NOT Do

- ❌ Do not register policies manually in `AuthServiceProvider` — Laravel 13 auto-discovers them by naming convention
- ❌ Do not remove the `Domain::where('user_id', auth()->id())` query scope from `index()` — the policy alone does not filter list queries
- ❌ Do not remove the `abort_if($concept->domain_id !== $domain->id, 404)` check — the policy does not verify URL structure
- ❌ Do not lazy-load relations inside policy methods — always load before calling `authorize()`
- ❌ Do not add a `before()` method to policies that returns `true` for all users — it bypasses all checks
- ❌ Do not use `Gate::allows()` directly in controllers — always use `$this->authorize()` via the controller trait
- ❌ Do not forget that `restore()` and `forceDelete()` need their own policy methods — they are not covered by `delete()`

---

## 11. Acceptance Criteria

- [ ] A user accessing `/domains/{domain}` for a domain they don't own gets a 403 response
- [ ] A user accessing `/domains/{domain}/concepts/{concept}` for another user's concept gets a 403
- [ ] A user trying to generate questions for another user's concept gets a 403
- [ ] A user trying to restore or force-delete another user's archived domain/concept/question gets a 403
- [ ] A user can perform all CRUD actions on their own domains, concepts, and generated questions without any 403
- [ ] Manipulating the URL to access a concept from a different domain returns 404 (not 403)
- [ ] No `if ($x->user_id !== auth()->id()) abort(403)` lines remain anywhere in controllers
