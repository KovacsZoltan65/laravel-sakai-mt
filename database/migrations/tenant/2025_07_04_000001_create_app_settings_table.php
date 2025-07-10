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
        Schema::create('app_settings', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset('utf8mb3');
            $table->collation('utf8mb3_unicode_ci');
            
            $table->id()->comment('Rekord azonosító');
            $table->string('key')->unique()->index()->comment('Paraméter kulcsa');
            $table->text('value')->comment('Paraméter értéke');
            $table->enum('type', ['string', 'int', 'bool', 'json', 'timezone'])->comment('Paraméter típusa');
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
        Schema::dropIfExists('app_settings');
    }
};
