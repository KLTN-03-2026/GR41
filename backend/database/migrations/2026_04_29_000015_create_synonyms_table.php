<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('synonyms', function (Blueprint $table) {
            $table->id();
            $table->string('keyword', 100)->index();
            $table->json('synonyms');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('synonyms');
    }
};
