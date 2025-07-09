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
        Schema::create('company_settings', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset('utf8mb3');
            $table->collation('utf8mb3_unicode_ci');
            
            $table->id()->comment('Rekord azonosító');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('key')->index()->comment('Paraméter kulcsa');
            $table->text('value')->comment('Paraméter értéke');
            $table->enum('type', ['string', 'int', 'bool', 'json'])->comment('Paraméter típusa');
            $table->boolean('active')->default(1)->index()->comment('Aktív');
            
            $table->unique(['company_id', 'key'], 'company_settings_company_key_unique');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
