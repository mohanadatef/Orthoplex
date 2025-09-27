<?php

namespace App\Jobs;

use App\Models\WebhookDlq;
use App\Services\WebhookService;
use Illuminate\Queue\Jobs\Job;

class RetryDlqWebhooksJob extends Job
{
    public function handle(WebhookService $service)
    {
        WebhookDlq::limit(10)->get()->each(function($dlq) use ($service) {
            $webhook = new \App\Models\Webhook($dlq->toArray());
            $service->send($webhook);
        });
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
