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
        Schema::connection('tenant')->create('employees', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset('utf8mb3');
            $table->collation('utf8mb3_unicode_ci');
            
            $table->id()->comment('Rekord azonosító');
            $table->string('name')->comment('Név');
            $table->string('position')->comment('Pozíció');
            $table->string('email')->unique()->comment('Email cím');
            
            $table->boolean('active')->default(1)->index()->comment('Aktív');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('employees');
    }
};
