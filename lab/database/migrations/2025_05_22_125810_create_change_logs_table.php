<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('change_logs', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type'); // User, Role, Permission
            $table->unsignedBigInteger('entity_id');
            $table->unsignedBigInteger('created_by')->comment('Кто совершил изменение');
            $table->string('action')->comment('create, update, delete, restore');
            $table->json('before')->nullable()->comment('Данные до изменения');
            $table->json('after')->nullable()->comment('Данные после изменения');
            $table->boolean('is_rollbacked')->default(false);
            $table->timestamp('rollbacked_at')->nullable();
            $table->foreignId('rollbacked_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('change_logs');
    }
};
