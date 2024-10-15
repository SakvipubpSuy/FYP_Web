<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CardTiersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('card_tiers')->insert(
            [
                ['card_tier_name' => 'Rare', 'card_XP' => '100','card_energy_required' => '20', 'color' => '#49A6EF', 'card_RP_required' => '0'],
                ['card_tier_name' => 'Epic', 'card_XP' => '200','card_energy_required' => '40', 'color' => '#C23ADA', 'card_RP_required' => '20'],
                ['card_tier_name' => 'Legendary', 'card_XP' => '300','card_energy_required' => '60', 'color' => '#FBEC6', 'card_RP_required' => '40'],
            ]   
        );
    }
}
