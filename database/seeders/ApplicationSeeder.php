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
                'credentials' => array(
                    'ANDROID' => array(
                        'username' => 'android_username',
                        'password' => 'android_password'
                    ),
                    'IOS' => array(
                        'username' => 'ios_username',
                        'password' => 'ios_password'
                    )
                ),
            ]);
        }
    }
}
