<?php

namespace App\Jobs;

use App\Models\LoginEvent;
use App\Models\Org;
use App\Models\Webhook;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Facades\DB;

class DispatchLoginEventsBatchJob extends Job
{
    public function handle()
    {
        $events = LoginEvent::whereNull('batched_at')->limit(100)->get();
        if ($events->isEmpty()) return;

        $grouped = $events->groupBy('org_id');

        foreach ($grouped as $orgId => $orgEvents) {
            $org = Org::find($orgId);
            if (! $org || ! $org->webhook_secret) continue;

            Webhook::create([
                'org_id'  => $orgId,
                'url'     => $org->webhook_url,
                'event'   => 'login.batch',
                'payload' => $orgEvents->toArray(),
                'status'  => 'pending',
            ]);
        }

        DB::table('login_events')
            ->whereIn('id', $events->pluck('id'))
            ->update(['batched_at' => now()]);
    }

    public function getJobId()
    {
        // TODO: Implement getJobId() method.
    }

    public function getRawBody()
    {
        // TODO: Implement getRawBody() method.
    }
}
