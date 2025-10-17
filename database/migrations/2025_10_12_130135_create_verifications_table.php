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
    Schema::create('verifications', function (Blueprint $table) {
        $table->id();
        $table->foreignId('corporate_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
        $table->foreignId('admin_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
        $table->enum('status', ['pending','approved','rejected'])->default('pending');
        $table->text('note')->nullable();
        $table->timestamp('verified_at')->nullable();
        $table->timestamps();

        $table->unique('corporate_id'); // satu corporate = satu verifikasi
     });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifications');
    }
};
