<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_intents', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->string('intent_key', 50)->unique();
            $table->string('name', 100);
            $table->json('keywords');
            $table->text('response_template');
            $table->string('data_source', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_intents');
    }
};
