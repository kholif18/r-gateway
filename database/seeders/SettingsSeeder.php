<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::setGlobal([
            'app_name' => 'R-Gateway',
            'app_version' => '1.0.0',
            'update_url' => null,
        ]);
    }
}
