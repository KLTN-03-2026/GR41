<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->enum('status', ['pending', 'published', 'rejected'])->default('published')->after('is_featured');
            $table->foreignId('proposed_by')->nullable()->constrained('users')->nullOnDelete()->after('uploaded_by');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete()->after('proposed_by');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            $table->text('rejection_reason')->nullable()->after('reviewed_at');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['proposed_by']);
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['status', 'proposed_by', 'reviewed_by', 'reviewed_at', 'rejection_reason']);
        });
    }
};
