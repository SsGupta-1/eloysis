<?php

namespace Database\Seeders;

use App\Models\Periods;
use Illuminate\Database\Seeder;

class periodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $periods = [
            ['name' => 'Period 1', 'start_time' => '09:00:00', 'end_time' => '09:50:00', 'sort_order' => 1],
            ['name' => 'Period 2', 'start_time' => '09:50:00', 'end_time' => '10:40:00', 'sort_order' => 2],
            ['name' => 'Period 3', 'start_time' => '10:40:00', 'end_time' => '11:30:00', 'sort_order' => 3],
            ['name' => 'Period 4', 'start_time' => '11:30:00', 'end_time' => '12:20:00', 'sort_order' => 4],
            ['name' => 'Break',    'start_time' => '12:20:00', 'end_time' => '13:00:00', 'sort_order' => 5],
            ['name' => 'Period 5', 'start_time' => '13:00:00', 'end_time' => '13:50:00', 'sort_order' => 6],
            ['name' => 'Period 6', 'start_time' => '13:50:00', 'end_time' => '14:40:00', 'sort_order' => 7],
            ['name' => 'Period 7', 'start_time' => '14:40:00', 'end_time' => '15:30:00', 'sort_order' => 8],
            ['name' => 'Period 8', 'start_time' => '15:30:00', 'end_time' => '16:20:00', 'sort_order' => 9],
        ];

        foreach ($periods as $period) {
            Periods::updateOrCreate(
                ['name' => $period['name']],
                [
                    'start_time' => $period['start_time'],
                    'end_time' => $period['end_time'],
                    'sort_order' => $period['sort_order'],
                ]
            );
        }
    }
}
