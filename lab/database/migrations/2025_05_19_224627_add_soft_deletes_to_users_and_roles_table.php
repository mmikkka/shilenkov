<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users_and_roles', function (Blueprint $table) {
            $table->softDeletes();
            $table->dropUnique(['user_id', 'role_id']);
            $table->unique(['user_id', 'role_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_and_roles', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'role_id', 'deleted_at']);
            $table->unique(['user_id', 'role_id']);
            $table->dropSoftDeletes();
        });
    }
};
