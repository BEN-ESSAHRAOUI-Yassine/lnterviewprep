<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService
{
    public function generateInterviewQuestions(string $title, string $explanation, string $difficultyLabel, string $statusLabel): array
    {
        $prompt = "You are a senior technical interviewer. Generate exactly 5 realistic and distinct technical interview questions for the following concept.

Concept title: {$title}
Difficulty level: {$difficultyLabel}
Mastery status: {$statusLabel}
Explanation: {$explanation}

Return ONLY a JSON array of 5 strings. No explanation, no preamble, no markdown. Example format:
[\"Question 1?\", \"Question 2?\", \"Question 3?\", \"Question 4?\", \"Question 5?\"]";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.groq.key'),
            'Content-Type' => 'application/json',
        ])->post(config('services.groq.url'), [
            'model' => config('services.groq.model'),
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.7,
            'max_tokens' => 800,
        ]);

        if ($response->failed()) {
            Log::error('Groq API failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Erreur lors de la communication avec l\'API Groq. Veuillez réessayer plus tard.');
        }

        $content = $response->json('choices.0.message.content');

        if (!$content) {
            throw new \RuntimeException('Réponse invalide de l\'API Groq.');
        }

        $content = trim($content);
        if (preg_match('/\[.*\]/s', $content, $matches)) {
            $content = $matches[0];
        }

        $questions = json_decode($content, true);

        if (!is_array($questions) || count($questions) !== 5) {
            Log::warning('Groq response did not return exactly 5 questions', [
                'response' => $content,
                'parsed' => $questions,
            ]);
            throw new \RuntimeException('Format de réponse inattendu de l\'API Groq.');
        }

        return $questions;
    }
}