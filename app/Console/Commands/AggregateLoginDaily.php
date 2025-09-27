<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AggregateLoginDaily extends Command
{
    protected $signature = 'analytics:aggregate-logins';
    protected $description = 'Aggregate daily login events into login_daily table';

    public function handle(): int
    {
        $yesterday = now()->subDay()->toDateString();

        $data = DB::table('login_events')
            ->select('user_id', DB::raw('DATE(created_at) as day'), DB::raw('COUNT(*) as total'))
            ->whereDate('created_at', $yesterday)
            ->groupBy('user_id', 'day')
            ->get();

        foreach ($data as $row) {
            DB::table('login_daily')->updateOrInsert(
                ['user_id' => $row->user_id, 'day' => $row->day],
                ['count' => $row->total]
            );
        }

        $this->info("Aggregated logins for {$yesterday}");

        return Command::SUCCESS;
    }
}
