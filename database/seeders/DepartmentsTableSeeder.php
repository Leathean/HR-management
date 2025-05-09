<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('departments')->insert([
            ['DP_NAME' => 'CET', 'DP_DESCRIPTION' => 'College of Engineering and Technology'],
            ['DP_NAME' => 'COB', 'DP_DESCRIPTION' => 'College of Business'],
            ['DP_NAME' => 'CRM', 'DP_DESCRIPTION' => 'College of Criminology'],
            ['DP_NAME' => 'CHTM', 'DP_DESCRIPTION' => 'College of Hotel Management'],
            ['DP_NAME' => 'CTE', 'DP_DESCRIPTION' => 'College of Teaching Education'],
        ]);
    }
}
