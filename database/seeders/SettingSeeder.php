<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('settings')->insertOrIgnore([
            [
                'key' => 'rate_limit_limit',
                'value' => '5',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'rate_limit_decay',
                'value' => '60',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
