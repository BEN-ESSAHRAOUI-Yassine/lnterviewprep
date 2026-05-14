<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Concept extends Model
{
    use SoftDeletes;

    protected $fillable = ['domain_id', 'title', 'explanation', 'difficulty', 'status'];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function generatedQuestions(): HasMany
    {
        return $this->hasMany(GeneratedQuestion::class);
    }

    public function delete(): bool
    {
        foreach ($this->generatedQuestions()->withTrashed()->get() as $question) {
            $question->delete();
        }
        return parent::delete();
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'to_review' => 'À revoir',
            'in_progress' => 'En cours',
            'mastered' => 'Maîtrisé',
        };
    }

    public function getDifficultyLabelAttribute(): string
    {
        return match ($this->difficulty) {
            'junior' => 'Junior',
            'mid' => 'Mid',
            'senior' => 'Senior',
        };
    }
}