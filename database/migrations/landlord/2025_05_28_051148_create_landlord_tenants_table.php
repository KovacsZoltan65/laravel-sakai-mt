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

            $table->id()->comment('Rekord azonosító');
            $table->string('name')->comment('Példány neve');
            $table->string('domain')->unique()->comment('Domain név');
            $table->string('host', 125)->default('localhost')->comment('Adatbázis szerver');
            $table->integer('port')->default(3306)->comment('Adatbázis port');
            $table->string('database')->unique()->comment('Adatbázis név');
            $table->string('username')->comment('Adatbázis felhasználó');
            $table->string('password')->comment('Adatbázis jelszó');
            $table->boolean('active')->default(1)->index()->comment('Aktív');
            $table->tinyInteger('locaked')->default(0)->comment('Zárolás');

            $table->timestamps();
        });
    }
};
