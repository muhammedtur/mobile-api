<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

use App\Models\Application;

use Uuid;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // If no entry has been record before
        if (Application::count() < 1) {
            Application::firstOrCreate([
                'guid' => Uuid::generate()->string,
                'name' => "Test",
                'username' => "application1_username",
                'password' => "application1_password"
            ]);
        }
    }
}
