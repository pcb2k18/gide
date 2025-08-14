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
    Schema::create('biographies', function (Blueprint $table) {
        $table->id();
        $table->string('slug')->unique();
        $table->string('full_name')->index();
        
        // This single column will hold our entire structured content object
        $table->json('content_data'); 
        
        $table->timestamps(); // Standard created_at and updated_at
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biographies');
    }
};
