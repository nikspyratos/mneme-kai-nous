<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\LoadsheddingSchedule;
use Illuminate\Database\Seeder;

class LoadsheddingScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Kenilworth',
                'zone' => '11',
                'api_id' => 'capetown-11-kenilworth',
                'region' => 'City of Cape Town',
                'is_home' => true,
            ],
            [
                'name' => 'Cape Town CBD & Atlantic Seaboard',
                'zone' => '7',
                'api_id' => 'capetown-7-capetowncbd',
                'region' => 'City of Cape Town',
                'is_home' => false,
            ],
        ];
        foreach ($data as $datum) {
            LoadsheddingSchedule::firstOrCreate($datum);
        }
    }
}
