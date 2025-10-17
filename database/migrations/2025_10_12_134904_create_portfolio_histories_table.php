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
    Schema::create('portfolio_histories', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
        $table->enum('type', [
            'competition',      
            'certification', 
            'training',         
            'publication',      
            'achievement',      
        ]);
        $table->string('title'); 
        $table->string('issuer')->nullable();
        $table->date('date')->nullable();
        $table->string('attachment')->nullable(); 
        $table->text('description')->nullable(); 
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolio_histories');
    }
};
