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
        Schema::create('log_requests', function (Blueprint $table) {
            $table->id();
            $table->string('url', 2048); // Полный URL
            $table->string('method', 10); // HTTP метод
            $table->string('controller')->nullable(); // Контроллер
            $table->string('action')->nullable(); // Метод контроллера
            $table->json('request_body')->nullable(); // Тело запроса
            $table->json('request_headers')->nullable(); // Заголовки запроса
            $table->unsignedBigInteger('user_id')->nullable(); // ID пользователя
            $table->string('ip_address', 45); // IPv6 support
            $table->text('user_agent')->nullable(); // User-Agent
            $table->integer('status_code'); // Статус ответа
            $table->json('response_body')->nullable(); // Тело ответа
            $table->json('response_headers')->nullable(); // Заголовки ответа
            $table->timestamp('called_at'); // Время вызова

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_requests');
    }
};
