<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ejobsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('ejobs')->insert([
            ['EJOB_NAME' => 'HR', 'EJOB_DESCRIPTION' => 'Human Resources Department'],
            ['EJOB_NAME' => 'IT', 'EJOB_DESCRIPTION' => 'Information Technology Department'],
            ['EJOB_NAME' => 'Finance', 'EJOB_DESCRIPTION' => 'Finance and Accounting Department'],
            ['EJOB_NAME' => 'Marketing', 'EJOB_DESCRIPTION' => 'Marketing and Advertising Department'],
            ['EJOB_NAME' => 'Sales', 'EJOB_DESCRIPTION' => 'Sales and Business Development Department'],
        ]);
    }
}



