<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RefillUserEnergy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refill:user-energy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refill user energy everyday at 0500 UTC+7';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Update query to refill user energy
        DB::table('users')
        ->where('energy', '<', 160)
        ->update(['energy' => 160]);
        $this->info('User energy refilled successfully.');
    }
}
