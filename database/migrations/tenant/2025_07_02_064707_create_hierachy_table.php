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
        Schema::create('hierarchy', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('employees')->onDelete('cascade');
            $table->foreignId('child_id')->constrained('employees')->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hierachy');
    }
};
