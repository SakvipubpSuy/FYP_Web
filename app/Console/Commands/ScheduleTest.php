<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use illuminate\Support\Facades\Log;
class ScheduleTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'TEST';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        echo "Hello World";
    }
}
