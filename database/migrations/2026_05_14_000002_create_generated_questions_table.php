<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('concept_id')->constrained()->onDelete('cascade');
            $table->json('questions');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_questions');
    }
};