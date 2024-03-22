<?php

namespace Database\Seeders;

use App\Models\Reward;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RewardsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rewards = Reward::DEFAULT_REWARDS;
        DB::table('rewards')->truncate();
        $rewardsData = [];
        foreach ($rewards as $key => $reward) {
            $rewardsData[] = [
                'name'  => $key,
                'bonus' => $reward['bonus'],
                'type'  => $reward['type'],
            ];
        }
        DB::table('rewards')->insert($rewardsData);
    }
}
