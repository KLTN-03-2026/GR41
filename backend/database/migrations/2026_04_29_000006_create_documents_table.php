<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('category_id');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title', 255)->index();
            $table->string('slug', 280)->unique();
            $table->text('description')->nullable();
            $table->string('author', 150)->nullable();
            $table->string('publisher', 150)->nullable();
            $table->year('published_year')->nullable();
            $table->string('isbn', 20)->nullable();
            $table->string('language', 10)->default('vi');
            $table->integer('pages')->nullable();
            $table->string('file_url', 500);
            $table->string('cover_image', 500)->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('download_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('restrict');
            $table->index(['category_id', 'is_featured', 'created_at'], 'idx_doc_category');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE documents ADD FULLTEXT INDEX ft_search (title, description, author)');
            DB::statement('CREATE INDEX idx_doc_views ON documents (view_count DESC)');
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            Schema::table('documents', function (Blueprint $table): void {
                $table->dropIndex('idx_doc_views');
                $table->dropFullText(['title', 'description', 'author']);
            });
        }

        Schema::dropIfExists('documents');
    }
};
