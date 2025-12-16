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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            // 🟢 RELATIONS
            // If a Category is deleted, stop! Don't delete the assets inside it.
            $table->foreignId('category_id')->constrained()->onDelete('restrict');

            // 🟢 DATA
            $table->string('name');
            // Unique Index: Two assets cannot share a serial number.
            $table->string('serial_number')->unique();
            $table->string('status')->default('available'); // available, assigned, broken
            $table->string('image_path')->nullable();

            $table->timestamps();
            $table->softDeletes(); // Never actually delete expensive equipment records
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
