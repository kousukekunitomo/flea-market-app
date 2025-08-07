<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConditionSeeder extends Seeder
{
    public function run(): void
    {
        $conditions = [
            ['condition' => '新品'],
            ['condition' => 'ほぼ新品'],
            ['condition' => '良好'],
            ['condition' => '使用感あり'],
        ];

        DB::table('conditions')->insert($conditions);
    }
}
