<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         \App\Models\User::factory()->create([
             'name' => 'test',
             'email' => 'test@mail.ru',
             'password' => Hash::make('123123123'),
         ]);

         \App\Models\User::factory(9)->create();

        $this->call([
            TaskSeeder::class,
        ]);
    }
}
