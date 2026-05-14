# AGENTS.md

**Project:** InterviewPrep  
**Author:** BEN-ESSAHRAOUI Yassine  
**Coding Agent:** OpenCode (opencode.ai)  
**AI API:** Groq API  
**Stack:** Laravel 13 · Blade · MySQL  
**Started:** 2026-05-11

---

## 1. Purpose of This File

This file defines how OpenCode is used throughout this project — what it is allowed to do, what it must never do, how each session must be structured, and what conventions must be followed in every generated output.

Any developer (or agent) reading this file must follow these rules without exception.

---

## 2. Agent Identity

| Field | Value |
|---|---|
| Agent | OpenCode |
| Mode | Terminal / CLI |
| Access | Full project filesystem |
| AI backend used inside OpenCode | Provider configured in OpenCode settings |
| API used for the app feature | Groq API (`api.groq.com`) |

---

## 3. Mandatory Session Structure

Every OpenCode session working on a feature **must** follow this exact order:

### Step 1 — Plan Mode First
Before generating any code, run OpenCode in **Plan mode**:
- Describe the feature in plain language
- State the files that will be created or modified
- State the database tables involved
- State what you explicitly do NOT want
- Review the plan output — adjust if needed

### Step 2 — Build Mode Second
Only after the plan is validated, switch to **Build mode**:
- Agent generates the code
- Developer reviews every file before accepting
- Developer manually adjusts anything that deviates from the spec

### Step 3 — Commit
After each feature or meaningful sub-feature:
- Write an explicit commit message (see Section 7)
- Include `[AI]` tag in the message
- Note in the commit body what was generated vs what was changed manually

---

## 4. Project Structure the Agent Must Respect

```
app/
  Http/
    Controllers/
      DomainController.php
      ConceptController.php
      GeneratedQuestionController.php
      Auth/
        LoginController.php
        RegisterController.php
    Requests/
      StoreDomainRequest.php
      UpdateDomainRequest.php
      StoreConceptRequest.php
      UpdateConceptRequest.php
  Models/
    User.php
    Domain.php
    Concept.php
    GeneratedQuestion.php
  Policies/
    DomainPolicy.php
    ConceptPolicy.php
    GeneratedQuestionPolicy.php
  Services/
    GroqService.php
database/
  migrations/
  seeders/
resources/
  views/
    layouts/
      app.blade.php
    auth/
      login.blade.php
      register.blade.php
    domains/
      index.blade.php
      create.blade.php
      edit.blade.php
      show.blade.php
      archived.blade.php
    concepts/
      index.blade.php
      create.blade.php
      edit.blade.php
      show.blade.php
    generated-questions/
      (partials only — rendered inside concepts/show.blade.php)
specs/
AGENTS.md
.env
.env.example
```

---

## 5. Database Schema

The agent must use **exactly** these four tables. No extra tables. No renaming.

### `users`
| Column | Type | Notes |
|---|---|---|
| id | bigint unsigned | PK, auto-increment |
| name | varchar(255) | |
| email | varchar(255) | unique |
| password | varchar(255) | hashed |
| remember_token | varchar(100) | nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

### `domains`
| Column | Type | Notes |
|---|---|---|
| id | bigint unsigned | PK, auto-increment |
| user_id | bigint unsigned | FK → users.id, cascade delete |
| name | varchar(255) | |
| color | varchar(50) | hex or named color for badge |
| deleted_at | timestamp | nullable — soft delete |
| created_at | timestamp | |
| updated_at | timestamp | |

### `concepts`
| Column | Type | Notes |
|---|---|---|
| id | bigint unsigned | PK, auto-increment |
| domain_id | bigint unsigned | FK → domains.id, cascade delete |
| title | varchar(255) | |
| explanation | text | |
| difficulty | enum('junior','mid','senior') | |
| status | enum('to_review','in_progress','mastered') | default: to_review |
| deleted_at | timestamp | nullable — soft delete |
| created_at | timestamp | |
| updated_at | timestamp | |

### `generated_questions`
| Column | Type | Notes |
|---|---|---|
| id | bigint unsigned | PK, auto-increment |
| concept_id | bigint unsigned | FK → generated_questions.id, cascade delete |
| questions | json | array of 5 question strings |
| deleted_at | timestamp | nullable — soft delete |
| created_at | timestamp | |
| updated_at | timestamp | |

---

## 6. Eloquent Relations the Agent Must Define

### User
```php
public function domains(): HasMany // User → Domain
```

### Domain
```php
public function user(): BelongsTo
public function concepts(): HasMany  // includes soft-deleted scope when needed
```

### Concept
```php
public function domain(): BelongsTo
public function generatedQuestions(): HasMany
```

### GeneratedQuestion
```php
public function concept(): BelongsTo
```

**Soft delete cascades:**
- Soft-deleting a Domain → must cascade soft-delete all its Concepts → must cascade soft-delete all their GeneratedQuestions
- Soft-deleting a Concept → must cascade soft-delete all its GeneratedQuestions
- This cascade must be implemented in the Model `delete()` override or via an Observer — NOT via database-level cascade (which doesn't trigger soft deletes)

---

## 7. Accessors the Agent Must Implement

### In `Concept` model:

```php
// Returns: 'À revoir' | 'En cours' | 'Maîtrisé'
public function getStatusLabelAttribute(): string

// Returns: 'Junior' | 'Mid' | 'Senior'
public function getDifficultyLabelAttribute(): string
```

These must be used in all Blade views and passed to the Groq prompt.

---

## 8. Groq API Integration Rules

- HTTP call via Laravel `Http::` facade only — no external packages
- API key stored in `.env` as `GROQ_API_KEY` — never hardcoded
- API key must appear in `.env.example` as `GROQ_API_KEY=` (empty value)
- Endpoint: `https://api.groq.com/openai/v1/chat/completions`
- Model: `llama3-8b-8192` (or latest available free model)
- The prompt must include the concept `title`, `explanation`, `status_label`, and `difficulty_label`
- Response must be parsed and saved to `generated_questions.questions` as JSON before display
- If the API call fails (timeout, bad response, 4xx/5xx): catch the exception, do not crash, show a clean Blade error message
- Never display a blank page on API failure

### Example `.env` entries the agent must reference:
```
GROQ_API_KEY=
GROQ_MODEL=llama3-8b-8192
GROQ_API_URL=https://api.groq.com/openai/v1/chat/completions
```

---

## 9. Form Request Validation Rules

The agent must generate a dedicated `FormRequest` class for every create and update operation. Inline `$request->validate()` is not acceptable.

| Request Class | Rules |
|---|---|
| `StoreDomainRequest` | name: required, string, max:255 — color: required, string, max:50 |
| `UpdateDomainRequest` | same as Store |
| `StoreConceptRequest` | title: required, string, max:255 — explanation: required, string — difficulty: required, in:junior,mid,senior — status: sometimes, in:to_review,in_progress,mastered |
| `UpdateConceptRequest` | same as Store |

---

## 10. N+1 Prevention Rules

The agent must use eager loading on every controller method that returns a list:

```php
// DomainController@index
Domain::withCount(['concepts', 'concepts as mastered_count' => fn($q) => $q->where('status', 'mastered')])->get();

// ConceptController@index
$domain->concepts()->with('generatedQuestions')->get();
```

Laravel Debugbar must be installed in dev (`composer require barryvdh/laravel-debugbar --dev`) and queries verified to be N+1-free before each commit.

---

## 11. Authentication Rules

- Use Laravel's built-in Auth (manual implementation — no Breeze, no Jetstream, no Fortify)
- Session-based authentication
- All routes except login/register must be behind `auth` middleware
- Ownership is enforced via Laravel Policy classes — see Section 11a and `specs/07-policies.md`

---

## 11a. Authorization Policy Rules

Ownership across the entire app is enforced using three Laravel Policy classes. Controllers must use `$this->authorize()` — never manual `abort(403)`.

### Policy classes
| Policy | Model | Ownership check |
|---|---|---|
| `DomainPolicy` | `Domain` | `$user->id === $domain->user_id` |
| `ConceptPolicy` | `Concept` | `$user->id === $concept->domain->user_id` |
| `GeneratedQuestionPolicy` | `GeneratedQuestion` | `$user->id === $generatedQuestion->concept->domain->user_id` |

### Policy methods each class must implement
```
viewAny, view, create, update, delete, restore, forceDelete
```

### Controller usage pattern
```php
// For model instances:
$this->authorize('view', $domain);
$this->authorize('update', $concept);
$this->authorize('delete', $generatedQuestion);

// For create (no instance yet):
$this->authorize('create', Domain::class);
$this->authorize('create', Concept::class);
```

### Additional rule — always keep query scope even with policies
Policies gate individual actions but do not filter list queries. The `index()` query must still scope to `auth()->id()`:
```php
Domain::where('user_id', auth()->id())->...
```

### Additional rule — always verify parent in nested routes
Policies check ownership, but not URL consistency. Always verify:
```php
abort_if($concept->domain_id !== $domain->id, 404);
```

### Eager loading before policy calls
- For `Concept` actions: `$concept->domain` must be accessible (Eloquent lazy-load acceptable for single record)
- For `GeneratedQuestion` actions: always call `$question->load('concept.domain')` before `authorize()`

### Auto-discovery
Laravel 13 auto-discovers policies by naming convention (`ModelPolicy`). Do NOT register them manually in `AuthServiceProvider`.

---

## 12. Blade Conventions

- Single layout file: `resources/views/layouts/app.blade.php`
- No Alpine.js, no Livewire, no JavaScript frameworks
- Status quick-change (US9) via a simple HTML `<form method="POST">` with `@method('PATCH')`
- Archived concept checkbox toggle: a simple `<form method="GET">` that appends `?show_archived=1` to the URL, handled in the controller
- Archived question checkbox toggle: same pattern — `?show_archived=1` on concept show page

---

## 13. What the Agent Must NEVER Do

- ❌ Never install Breeze, Jetstream, Fortify, or any auth scaffold package
- ❌ Never use `$request->validate()` directly in controllers — always use Form Request classes
- ❌ Never hardcode the Groq API key anywhere in PHP files
- ❌ Never commit `.env` — only `.env.example`
- ❌ Never use Alpine.js, Livewire, or any JS framework
- ❌ Never use database-level CASCADE for soft deletes — implement in models/observers
- ❌ Never generate routes outside `routes/web.php`
- ❌ Never create extra tables beyond the four defined above
- ❌ Never display a blank page on Groq API failure
- ❌ Never skip eager loading on list queries
- ❌ Never rename the four core tables
- ❌ Never use `if ($x->user_id !== auth()->id()) abort(403)` in controllers — always use `$this->authorize()` with a Policy class
- ❌ Never register policies manually in `AuthServiceProvider` — Laravel 13 auto-discovers them
- ❌ Never skip the `abort_if($concept->domain_id !== $domain->id, 404)` parent check — it is not replaced by policies
- ❌ Never lazy-load relations inside Policy methods — always load before calling `authorize()`
- ❌ Never add a `before()` method to policies that returns `true` — it bypasses all ownership checks

---

## 14. Route Naming Conventions

```
domains.index          GET    /domains
domains.create         GET    /domains/create
domains.store          POST   /domains
domains.show           GET    /domains/{domain}
domains.edit           GET    /domains/{domain}/edit
domains.update         PATCH  /domains/{domain}
domains.destroy        DELETE /domains/{domain}
domains.archived       GET    /domains/archived
domains.restore        PATCH  /domains/{domain}/restore
domains.forceDelete    DELETE /domains/{domain}/force-delete

concepts.index         GET    /domains/{domain}/concepts
concepts.create        GET    /domains/{domain}/concepts/create
concepts.store         POST   /domains/{domain}/concepts
concepts.show          GET    /domains/{domain}/concepts/{concept}
concepts.edit          GET    /domains/{domain}/concepts/{concept}/edit
concepts.update        PATCH  /domains/{domain}/concepts/{concept}
concepts.updateStatus  PATCH  /domains/{domain}/concepts/{concept}/status
concepts.destroy       DELETE /domains/{domain}/concepts/{concept}
concepts.restore       PATCH  /domains/{domain}/concepts/{concept}/restore
concepts.forceDelete   DELETE /domains/{domain}/concepts/{concept}/force-delete

generated-questions.store        POST   /concepts/{concept}/generate
generated-questions.destroy      DELETE /generated-questions/{generatedQuestion}
generated-questions.restore      PATCH  /generated-questions/{generatedQuestion}/restore
generated-questions.forceDelete  DELETE /generated-questions/{generatedQuestion}/force-delete
```

---

## 15. Commit Message Format

Every commit involving AI-generated code must follow this format:

```
[AI] <short description of what was built>

Agent: OpenCode
Generated: <list files the agent produced>
Modified manually: <list what you changed and why>
```

**Examples:**
```
[AI] scaffold Domain model, migration, and CRUD controller

Agent: OpenCode
Generated: Domain.php, create_domains_table migration, DomainController.php, StoreDomainRequest.php
Modified manually: Added withCount() eager loading in index() — agent used lazy loading
```

```
[AI] implement Groq API call in GroqService and concept show page

Agent: OpenCode
Generated: GroqService.php, updated ConceptController.php, updated concepts/show.blade.php
Modified manually: Added try/catch around Http:: call — agent did not handle timeout errors
```

---

## 16. Specs Folder

Every feature built with OpenCode must have a corresponding spec file in `specs/`.

| Feature | Spec File |
|---|---|
| Authentication | `specs/01-authentication.md` |
| Domains CRUD | `specs/02-domains-crud.md` |
| Concepts CRUD | `specs/03-concepts-crud.md` |
| AI Question Generation | `specs/04-ai-generation.md` |
| Soft Deletes & Restore | `specs/05-soft-deletes-restore.md` |
| Dashboard | `specs/06-dashboard.md` |
| Authorization Policies | `specs/07-policies.md` |

Each spec file must be committed before the feature branch is opened.
