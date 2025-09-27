<?php

return [
    'domain' => null,
    'path' => 'horizon',
    'use' => 'default',
    'prefix' => env('HORIZON_PREFIX', 'horizon:'),
    'middleware' => ['web'],
    'waits' => [
        'redis:default' => 60,
    ],
    'trim' => 24,
    'environments' => [
        'production' => [
            'supervisor-1' => [
                'maxProcesses' => 10,
                'balanceMaxShift' => 1,
                'balanceCooldown' => 3,
            ],
        ],
        'local' => [
            'supervisor-1' => [
                'maxProcesses' => 3,
            ],
        ],
    ],
];
