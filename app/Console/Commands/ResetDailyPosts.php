<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Group;

class ResetDailyPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-daily-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Group::query()->update(['posts_count' => 0]);
    }
}
