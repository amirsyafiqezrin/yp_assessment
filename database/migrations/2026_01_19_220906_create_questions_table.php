<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('questions')) {
            Schema::create('questions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
                $table->integer('type');
                $table->text('question_title');
                $table->json('question_options')->nullable();
                $table->text('question_answer');
                $table->integer('question_score')->default(1);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('questions')) {
            Schema::dropIfExists('questions');
        }
    }
};
