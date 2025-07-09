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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset('utf8mb3');
            $table->collation('utf8mb3_unicode_ci');
        
            $table->engine('InnoDB');
            $table->charset('utf8mb3');
            $table->collation('utf8mb3_unicode_ci');
            
            $table->id()->comment('Rekord azonosító');
            $table->string('label')->comment('Felirat');
            $table->string('icon')->default('')->comment('Ikon');
            $table->string('can')->nullable()->comment('Jogosultság');
            $table->string('url')->nullable()->comment('URL');
            $table->string('route_name')->nullable()->comment('Laravel route neve');
            $table->integer('default_weight')->default(0)->comment('Alapértelmezett súly');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('Szülő menü azonosítója');
            $table->integer('order_index')->default(0)->comment('Megjelenítési sorrend');
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('menu_items')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
