<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AggregateLoginDaily extends Command
{
    protected $signature = 'logins:aggregate-daily';
    protected $description = 'Aggregate login events into login_daily table';

    public function handle(): void
    {
        $this->info('Aggregating login events...');

        $events = DB::table('login_events')
            ->selectRaw('user_id, DATE(created_at) as login_date, COUNT(*) as total')
            ->groupBy('user_id','date')
            ->get();

        foreach ($events as $row) {
            DB::table('login_daily')->updateOrInsert(
                [
                    'user_id'    => $row->user_id,
                    'date' => $row->login_date,
                ],
                [
                    'count'      => $row->total,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $this->info('Aggregation complete.');
    }
}
