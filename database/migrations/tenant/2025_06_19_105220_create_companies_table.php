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
        Schema::create('companies', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset('utf8mb3');
            $table->collation('utf8mb3_unicode_ci');
            
            $table->id()->comment('Rekord azonosító');
            $table->string('name')->comment('Cég neve');
            $table->string('email')->nullable()->index()->comment('Email cím');
            $table->string('address')->nullable()->comment('Cím');
            $table->string('phone')->nullable()->comment('Telefon');
            $table->integer('active')->default(1)->index()->comment('Aktív');

            $table->timestamps();
            $table->softDeletes()->comment('Lágy törlés dátuma');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
