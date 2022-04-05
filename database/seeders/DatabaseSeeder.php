<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            AdminSeeder::class,
            BankTableSeeder::class,
            // UserSeeder::class,
        ]);
        \App\Models\User::factory(100)
        ->hasCredit(10)
        ->hasDebit(10)
        ->create();
        // \App\Models\Credit::factory(100)->create();
        // \App\Models\Debit::factory(100)->create();
    }
}
