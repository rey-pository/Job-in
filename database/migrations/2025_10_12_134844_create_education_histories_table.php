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
    Schema::create('education_histories', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
        $table->string('institution');
        $table->string('degree')->nullable(); 
        $table->year('start_year')->nullable();
        $table->year('end_year')->nullable();
        $table->decimal('gpa', 3, 2)->nullable(); 
        $table->text('description')->nullable(); 
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education_histories');
    }
};
