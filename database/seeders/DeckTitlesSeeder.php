<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeckTitlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('deck_titles')->insert([
            ['min_percentage' => 0, 'max_percentage' => 25, 'title' => 'Wanderer'],
            ['min_percentage' => 26, 'max_percentage' => 50, 'title' => 'Explorer'],
            ['min_percentage' => 51, 'max_percentage' => 75, 'title' => 'Pathfinder'],
            ['min_percentage' => 76, 'max_percentage' => 100, 'title' => 'Master Adventurer'],
        ]);
    }
}
