<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_tag', function (Blueprint $table) {
            $table->unsignedBigInteger('document_id');
            $table->unsignedInteger('tag_id');

            $table->primary(['document_id', 'tag_id']);

            $table->foreign('document_id')->references('id')->on('documents')->cascadeOnDelete();
            $table->foreign('tag_id')->references('id')->on('tags')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_tag');
    }
};
