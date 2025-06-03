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
        /*
         * ============================================
         * Minta az "attachments" mező használatára
         * ============================================
         *  [
                {
                    "filename": "dokumentum.pdf",
                    "path": "/storage/emails/dokumentum.pdf",
                    "mime": "application/pdf",
                    "size": 24576
                },
                {
                    "filename": "kep.jpg",
                    "path": "/storage/emails/kep.jpg",
                    "mime": "image/jpeg",
                    "size": 102400
                }
            ]
         */
        Schema::create('emails', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset('utf8mb3');
            $table->collation('utf8mb3_unicode_ci');

            $table->id();
            $table->string('to')->comment('A fő címzett(ek) e-mail címe(i).');
            $table->string('cc')->nullable()->comment('Másolatot kapó címzett(ek) e-mail címe(i), opcionális.');
            $table->string('bcc')->nullable()->comment('Rejtett másolatot kapó címzett(ek) e-mail címe(i), opcionális');
            $table->string('subject')->comment('Az e-mail tárgya');
            $table->text('body')->nullable()->comment('Az e-mail szöveges tartalma');
            $table->json('attachments')->nullable()->comment('JSON formátumban tárolt csatolmányok listája');
            $table->enum('status', ['queued', 'sent', 'failed'])->default('queued')->comment('Az e-mail állapota (queued, sent, failed)');
            $table->timestamp('sent_at')->nullable()->comment('Az elküldés időpontja');
            $table->text('error_message')->nullable()->comment('Hibaüzenet, ha az e-mail küldése sikertelen volt');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};
