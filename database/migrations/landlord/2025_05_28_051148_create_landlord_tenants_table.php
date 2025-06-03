<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset('utf8mb3');
            $table->collation('utf8mb3_unicode_ci');

            $table->id();
            $table->string('name')->collation('utf8mb3_unicode_ci');
            $table->string('domain')->collation('utf8mb3_unicode_ci')->unique();
            $table->string('host', 125)->collation('utf8mb3_unicode_ci')->default('localhost')->comment('Adatbázis szerver címe');
            $table->integer('port')->default(3306)->comment('Adatbázis port');
            $table->string('database')->collation('utf8mb3_unicode_ci')->unique();
            $table->string('username')->collation('utf8mb3_unicode_ci')->comment('Adatbázis felhasználó');
            $table->string('password')->collation('utf8mb3_unicode_ci');
            $table->boolean('active')->default(1)->index()->comment('Aktív');

            $table->timestamps();
        });
    }
};
