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
        Schema::table('user_messengers', function (Blueprint $table) {
            $table->string('verification_code')->nullable()->after('messenger_user_id');
            $table->timestamp('verification_code_sent_at')->nullable()->after('verification_code');
            $table->timestamp('verification_code_expires_at')->nullable()->after('verification_code_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('user_messengers', function (Blueprint $table) {
            $table->dropColumn(['verification_code', 'verification_code_sent_at', 'verification_code_expires_at']);
        });
    }
};
