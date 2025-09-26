<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\LoginEvent;
use App\Models\LoginDaily;
use Carbon\Carbon;

class AggregateLoginDaily extends Command {
    protected $signature = 'analytics:aggregate-logins {date?}';
    protected $description = 'Aggregate login events into login_daily table';

    public function handle() {
        $date = $this->argument('date') ?? now()->toDateString();
        $start = Carbon::parse($date)->startOfDay();
        $end = Carbon::parse($date)->endOfDay();

        $rows = LoginEvent::whereBetween('occurred_at', [$start, $end])
            ->selectRaw('user_id, COUNT(*) as cnt')
            ->groupBy('user_id')->get();

        foreach ($rows as $r) {
            LoginDaily::updateOrCreate(
                ['date' => $date, 'user_id' => $r->user_id],
                ['count' => $r->cnt]
            );
        }

        $this->info('Aggregated for ' . $date);
    }
}
