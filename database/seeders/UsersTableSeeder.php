<?php

namespace Database\Seeders;

// database/seeders/UsersTableSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $accessLevels = ['HR', 'EMPLOYEE', 'ADMIN'];
        foreach (range(1, 10) as $index) {
            DB::table('users')->insert([
                    'name' => "User {$index}",
                'email' => "user{$index}@gmail.com",
                'password' => Hash::make("user{$index}@gmail.com"),
                'email_verified_at' => Carbon::now(),
                'ACCESS' => $accessLevels[array_rand($accessLevels)],
            ]);
        }
    }
}
