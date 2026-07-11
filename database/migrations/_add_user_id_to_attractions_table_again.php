<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if column exists before adding
        if (!Schema::hasColumn('attractions', 'user_id')) {
            Schema::table('attractions', function (Blueprint $table) {
                $table->foreignId('user_id')->after('id')->nullable()->constrained('users')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::table('attractions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};