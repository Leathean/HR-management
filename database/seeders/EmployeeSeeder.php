<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\Ejob;
use App\Models\Department;
use App\Models\User;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        // Instantiate Faker
        $faker = Faker::create();

        // Get valid IDs from the related tables
        $ejobs = Ejob::all()->pluck('id')->toArray(); // All valid ejob_ids
        $departments = Department::all()->pluck('id')->toArray(); // All valid department_ids
        $users = User::all(); // Get all users (assuming exactly 10 users)

        // Loop through each user and create a corresponding employee
        foreach ($users as $index => $user) {
            DB::table('employees')->insert([
                'FNAME' => $faker->firstName,
                'MNAME' => $faker->optional()->lastName,
                'LNAME' => $faker->lastName,
                'EMAIL' => $faker->unique()->safeEmail,
                'EMPLOYMENT_START' => $faker->date(),
                'ejob_id' => $faker->randomElement($ejobs), // Random valid ejob_id
                'department_id' => $faker->randomElement($departments), // Random valid department_id
                'PNUMBER' => $faker->optional()->phoneNumber,
                'users_id' => $user->id, // 1:1 user-to-employee relationship
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
