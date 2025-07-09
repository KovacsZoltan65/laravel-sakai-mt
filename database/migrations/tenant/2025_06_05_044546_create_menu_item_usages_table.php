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
        Schema::create('menu_item_usages', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset('utf8mb3');
            $table->collation('utf8mb3_unicode_ci');
        
            $table->engine('InnoDB');
            $table->charset('utf8mb3');
            $table->collation('utf8mb3_unicode_ci');
                    
            $table->id()->comment('Rekord azonosító');
            $table->unsignedBigInteger('menu_item_id')->comment('Menü azonosító');
            $table->unsignedBigInteger('user_id')->nullable()->comment('Felhasználó id');
            $table->integer('usage_count')->default(0)->comment('Használat számáló');
            $table->timestamps();

            $table->foreign('menu_item_id')->references('id')->on('menu_items')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_item_usages');
    }
};
