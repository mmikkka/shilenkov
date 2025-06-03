<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('messenger_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('messenger_id')->constrained()->onDelete('cascade');
            $table->text('message');
            $table->boolean('status')->default(false);
            $table->unsignedSmallInteger('attempt_number')->default(1);
            $table->timestamps();

            // Индексы для оптимизации запросов
            $table->index('user_id');
            $table->index('messenger_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messenger_logs');
    }
};
