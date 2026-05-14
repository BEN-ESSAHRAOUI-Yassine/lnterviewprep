# Spec 04 — AI Question Generation (Groq API)

**Feature:** Generate interview questions per concept, view history, delete a generation  
**User Stories:** US11, US12, US13  
**Branch:** `feature/ai-generation`  
**Agent:** OpenCode  
**Author:** BEN-ESSAHRAOUI Yassine  

---

## 1. Objective

When a user clicks "Generate Interview Questions" on a concept's detail page, the application sends the concept's title, explanation, difficulty label, and status label to the Groq API. The API returns 5 realistic technical interview questions. The response is parsed, saved to the `generated_questions` table, and displayed immediately on the concept's detail page. All past generations are visible as a history. Each generation batch can be deleted individually.

---

## 2. Context & Constraints

- HTTP call via Laravel `Http::` facade only — no external packages (no Guzzle directly, no SDK)
- API key loaded from `.env` as `GROQ_API_KEY` — never hardcoded in any PHP file
- Endpoint: `https://api.groq.com/openai/v1/chat/completions`
- Model: value from `GROQ_MODEL` env variable (default: `llama3-8b-8192`)
- Questions saved to DB as JSON before display — never display without saving first
- If the API fails: catch the exception, show a user-friendly Blade error message, do not crash, do not show a blank page
- Groq API integration logic lives in a dedicated `GroqService` class — not in the controller

---

## 3. Database

Table: `generated_questions`

| Column | Type | Notes |
|---|---|---|
| id | bigint unsigned | PK, auto-increment |
| concept_id | bigint unsigned | FK → concepts.id, onDelete cascade |
| questions | json | Array of exactly 5 question strings |
| deleted_at | timestamp | nullable — soft delete |
| created_at | timestamp | |
| updated_at | timestamp | |

---

## 4. Environment Variables

The following must exist in `.env` and `.env.example`:

```
GROQ_API_KEY=          ← real key in .env, empty in .env.example
GROQ_MODEL=llama3-8b-8192
GROQ_API_URL=https://api.groq.com/openai/v1/chat/completions
```

---

## 5. Files to Create

| File | Purpose |
|---|---|
| `database/migrations/xxxx_create_generated_questions_table.php` | Table with json column and soft deletes |
| `app/Models/GeneratedQuestion.php` | Model with SoftDeletes, fillable, casts, relations |
| `app/Services/GroqService.php` | Encapsulates the Groq API call logic |
| `app/Http/Controllers/GeneratedQuestionController.php` | Handles generate, destroy |

---

## 6. Files to Modify

| File | Change |
|---|---|
| `routes/web.php` | Add generation routes |
| `resources/views/concepts/show.blade.php` | Add generate button + history section |

---

## 7. Routes

```
POST   /concepts/{concept}/generate                              generated-questions.store
DELETE /generated-questions/{generatedQuestion}                 generated-questions.destroy    (soft delete)
```

Note: restore and force-delete routes are in Spec 05.

---

## 8. Model — `GeneratedQuestion.php`

```php
use SoftDeletes;

protected $fillable = ['concept_id', 'questions'];

protected $casts = [
    'questions' => 'array',
];

// Relations
public function concept(): BelongsTo
```

The `questions` column is cast to array so that `$generatedQuestion->questions` returns a PHP array of strings directly — no manual `json_decode()` needed in views or controllers.

---

## 9. Service — `GroqService.php`

```php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class GroqService
{
    public function generateInterviewQuestions(string $title, string $explanation, string $difficultyLabel, string $statusLabel): array
    {
        // Build prompt
        // Call Groq API via Http::
        // Parse the response
        // Return array of 5 question strings
        // Throw an exception if the API response is not usable
    }
}
```

### Prompt to send to Groq:

```
You are a senior technical interviewer. Generate exactly 5 realistic and distinct technical interview questions for the following concept.

Concept title: {$title}
Difficulty level: {$difficultyLabel}
Mastery status: {$statusLabel}
Explanation: {$explanation}

Return ONLY a JSON array of 5 strings. No explanation, no preamble, no markdown. Example format:
["Question 1?", "Question 2?", "Question 3?", "Question 4?", "Question 5?"]
```

### Groq API call structure:

```php
$response = Http::withHeaders([
    'Authorization' => 'Bearer ' . config('services.groq.key'),
    'Content-Type'  => 'application/json',
])->post(config('services.groq.url'), [
    'model'    => config('services.groq.model'),
    'messages' => [
        ['role' => 'user', 'content' => $prompt],
    ],
    'temperature' => 0.7,
    'max_tokens'  => 800,
]);
```

### Response parsing:

```php
$content = $response->json('choices.0.message.content');
$questions = json_decode($content, true);

if (!is_array($questions) || count($questions) !== 5) {
    throw new \RuntimeException('Invalid response from Groq API');
}

return $questions;
```

---

## 10. Config — `config/services.php`

Add:
```php
'groq' => [
    'key'   => env('GROQ_API_KEY'),
    'url'   => env('GROQ_API_URL', 'https://api.groq.com/openai/v1/chat/completions'),
    'model' => env('GROQ_MODEL', 'llama3-8b-8192'),
],
```

---

## 11. Controller — `GeneratedQuestionController.php`

### `store(Request $request, Concept $concept)`
- Verify `$concept->domain->user_id === auth()->id()` — abort(403) otherwise
- Instantiate `GroqService` (or inject via constructor)
- Wrap the service call in a `try/catch`
  - On success: create `GeneratedQuestion` with `concept_id` and parsed `questions` array, redirect back to `concepts.show` with success flash
  - On failure (any exception): redirect back to `concepts.show` with error flash message — do NOT crash the page

### `destroy(GeneratedQuestion $generatedQuestion)`
- Verify ownership via `$generatedQuestion->concept->domain->user_id === auth()->id()` — abort(403)
- Soft-delete the record
- Redirect back to `concepts.show`

---

## 12. View Integration — `concepts/show.blade.php`

Add two sections to the existing concept detail view:

### Section A — Generate Button
```blade
<form method="POST" action="{{ route('generated-questions.store', $concept) }}">
    @csrf
    <button type="submit">Generate Interview Questions</button>
</form>

@if(session('error'))
    <p class="error">{{ session('error') }}</p>
@endif
```

### Section B — Generation History
```blade
@forelse($concept->generatedQuestions as $generation)
    <div>
        <small>Generated on {{ $generation->created_at->format('d/m/Y H:i') }}</small>
        <ol>
            @foreach($generation->questions as $question)
                <li>{{ $question }}</li>
            @endforeach
        </ol>
        <form method="POST" action="{{ route('generated-questions.destroy', $generation) }}">
            @csrf
            @method('DELETE')
            <button type="submit">Delete this generation</button>
        </form>
    </div>
@empty
    <p>No questions generated yet.</p>
@endforelse
```

The concept's `generatedQuestions` must be eager-loaded in `ConceptController@show` — not lazy-loaded.

---

## 13. What the Agent Must NOT Do

- ❌ Do not hardcode the API key anywhere in PHP or Blade files
- ❌ Do not call the Groq API directly from the controller — use GroqService
- ❌ Do not display generated questions without saving them to the DB first
- ❌ Do not let the page crash or go blank if the API call fails — always redirect with an error flash
- ❌ Do not use any external HTTP package — only `Http::` facade
- ❌ Do not skip the `json_decode` validation — always check it's an array of 5 strings
- ❌ Do not forget to cast `questions` as `'array'` in the model
- ❌ Do not forget `@csrf` and `@method('DELETE')` in the delete form
- ❌ Do not commit `.env` with the real API key — only `.env.example` is committed

---

## 14. Acceptance Criteria

- [ ] Clicking "Generate Interview Questions" sends a call to Groq and returns 5 questions
- [ ] Questions are saved to `generated_questions` table before being displayed
- [ ] Each past generation is shown with its 5 questions and creation date
- [ ] Deleting a generation soft-deletes it — it disappears from the history
- [ ] If the API is unavailable or returns invalid data, a clean error message is shown
- [ ] The page never crashes or goes blank on API failure
- [ ] The API key is never visible in any committed PHP file
