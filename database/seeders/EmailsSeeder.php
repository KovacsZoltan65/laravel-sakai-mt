<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Email;
use Faker\Generator as Faker;
use Illuminate\Container\Container;
use Carbon\Carbon;

class EmailsSeeder extends Seeder
{
    protected $faker;

    public function __construct()
    {
        $this->faker = Container::getInstance()->make(Faker::class);
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //chema::disableForeignKeyConstraints();
        //Email::truncate();
        //Schema::enableForeignKeyConstraints();

        //$this->createEmails();

        for ($i = 0; $i < 10; $i++) {
            Email::create([
                'to' => $this->faker->unique()->safeEmail,
                'cc' => $this->faker->safeEmail,
                'bcc' => $this->faker->safeEmail,
                'subject' => $this->faker->sentence,
                'body' => $this->faker->paragraph,
                'attachments' => json_encode([
                    [
                        'filename' => $this->faker->word . '.pdf',
                        'path' => '/storage/emails/' . $this->faker->uuid . '.pdf',
                        'mime' => 'application/pdf',
                        'size' => $this->faker->numberBetween(10000, 500000),
                    ]
                ]),
                'status' => 'sent',
                'sent_at' => Carbon::now(),
                'error_message' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

    }
}