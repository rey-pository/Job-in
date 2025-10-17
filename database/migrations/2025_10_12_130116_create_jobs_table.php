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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('title');
            $table->string('department');
            $table->text('description');
            $table->string('province')->nullable();
            $table->string('city')->nullable();   
            $table->enum('work_type', ['on-site', 'remote', 'hybrid'])->default('on-site'); // <-- TAMBAHAN: Tipe Kerja

            $table->date('published_date');
            $table->date('expired_date');
            $table->enum('status', ['active', 'expired'])->default('active');
            $table->timestamps();

            $table->index(['company_id', 'status', 'published_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};