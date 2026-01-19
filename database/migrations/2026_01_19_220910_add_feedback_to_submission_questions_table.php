<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('submission_questions')) {
            if (!Schema::hasColumn('submission_questions', 'feedback')) {
                Schema::table('submission_questions', function (Blueprint $table) {
                    $table->text('feedback')->nullable()->after('status');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('submission_questions') && Schema::hasColumn('submission_questions', 'feedback')) {
            Schema::table('submission_questions', function (Blueprint $table) {
                $table->dropColumn('feedback');
            });
        }
    }
};
