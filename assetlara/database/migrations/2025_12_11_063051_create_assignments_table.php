<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            // 🟢 RELATIONS
            // 'cascade' is safe here ONLY because Users/Assets use SoftDeletes.
            // It only triggers on a Force Delete (Hard Delete).
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');

            // Who performed the action? (Manually pointing to 'users' table)
            $table->foreignId('assigned_by')->constrained('users');

            // 🟢 TIMELINE
            $table->timestamp('assigned_at');
            $table->timestamp('returned_at')->nullable(); // NULL = Currently Checked Out

            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
