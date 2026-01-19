<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            if (!Schema::hasColumn('users', 'role')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->integer('role')->default(0)->after('email');
                });
            }
            if (!Schema::hasColumn('users', 'class_id')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->foreignId('class_id')->nullable()->after('password')->constrained('classes')->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users')) {
            if (Schema::hasColumn('users', 'class_id')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropForeign(['class_id']);
                    $table->dropColumn(['class_id']);
                });
            }
            if (Schema::hasColumn('users', 'role')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropColumn(['role']);
                });
            }
        }
    }
};
