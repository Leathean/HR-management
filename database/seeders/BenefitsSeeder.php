<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BenefitsSeeder extends Seeder
{
    public function run()
    {
        DB::table('benefits')->insert([
            [
                'NAME' => 'PhilHealth',
                'DESCRIPTION' => 'Covers medical expenses and checkups.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'NAME' => 'SSS',
                'DESCRIPTION' => 'Social Security Service.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'NAME' => 'Life Insurance',
                'DESCRIPTION' => 'if die = $$$.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
