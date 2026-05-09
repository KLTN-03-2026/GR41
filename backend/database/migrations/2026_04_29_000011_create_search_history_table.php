<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('search_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('keyword', 255)->index();
            $table->integer('result_count');
            $table->timestamp('searched_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_history');
    }
};
