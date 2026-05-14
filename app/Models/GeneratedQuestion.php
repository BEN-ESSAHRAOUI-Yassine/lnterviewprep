<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneratedQuestion extends Model
{
    use SoftDeletes;

    protected $fillable = ['concept_id', 'questions'];

    protected $casts = [
        'questions' => 'array',
    ];

    public function concept(): BelongsTo
    {
        return $this->belongsTo(Concept::class);
    }
}