<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class RollupLoginDaily implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Simple rollup for yesterday (idempotent upsert)
        $date = now()->subDay()->toDateString();
        $rows = DB::table('login_events')
            ->selectRaw('user_id, org_id, DATE(occurred_at) as d, COUNT(*) as c')
            ->whereRaw('DATE(occurred_at) = ?', [$date])
            ->groupBy('user_id','org_id','d')
            ->get();
        foreach ($rows as $r) {
            DB::table('login_daily')->updateOrInsert(
                ['user_id'=>$r->user_id, 'org_id'=>$r->org_id, 'date'=>$r->d],
                ['count'=>DB::raw('COALESCE(count,0) + '.$r->c), 'updated_at'=>now(), 'created_at'=>now()]
            );
        }
    }
}
