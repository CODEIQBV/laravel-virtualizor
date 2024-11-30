#### VPS Statistics

```php
use CODEIQ\Virtualizor\VirtualizorAdmin;

// Get all VPS statistics
$stats = VirtualizorAdmin::vpsStats()->get();

// Get statistics for specific VPS
$stats = VirtualizorAdmin::vpsStats()->get([
    'vpsid' => 45
]);

// Get monthly statistics for specific VPS
$stats = VirtualizorAdmin::vpsStats()->get([
    'svs' => 45,              // VPS ID
    'show' => '202401'        // YYYYMM format
]);

// Get raw API response
$response = VirtualizorAdmin::vpsStats()->get(raw: true);
```

VPS Statistics Response:
```php
[
    'server' => [
        'bandwidth' => [
            'limit' => 5349.67,
            'used' => 5348.67,
            'usage' => [
                '1' => 0,
                '2' => 0,
                // ... daily usage
            ],
            'in' => [
                'usage' => [...],
                'used' => 4919.46,
                'limit' => 4920.46,
                'free' => 1
            ],
            'out' => [
                'usage' => [...],
                'used' => 429.2,
                'limit' => 430.2,
                'free' => 1
            ]
        ],
        'cpu' => [
            'manu' => 'intel',
            'cpumodel' => 'Intel(R) Pentium(R) CPU G620 @ 2.60GHz',
            'limit' => 2600,
            'used' => 2.6,
            'free' => 2597.4,
            'percent' => 0.1
        ],
        'ram' => [
            [
                'used_ram' => 806,
                'ram' => 1989
            ]
        ]
    ],
    'vps_data' => [
        '74' => [
            'status' => 1,
            'net_in' => 52,
            'net_out' => 0,
            'used_cpu' => '0.0',
            'used_ram' => '128',
            'ram' => '128',
            'used_disk' => 0.68,
            'disk' => '2',
            'used_bandwidth' => 0,
            'bandwidth' => 1,
            'virt' => 'kvm',
            'hostname' => 'www.mydomain.com'
        ]
    ],
    'timestamp' => 1536602202,
    'time_taken' => '2.156'
]
```

Monthly Statistics Response:
```php
[
    'vps_stats' => [
        [
            'vps_id' => 45,
            'timestamp' => 1570776006,
            'status' => true,
            'disk' => 0,
            'inode' => 0,
            'ram' => 0,
            'cpu' => [
                'usage' => 0.60,
                'actual' => 0.60
            ],
            'network' => [
                'in' => 1266,
                'out' => 0
            ],
            'io' => [
                'read' => 0,
                'write' => 0
            ]
        ]
    ],
    'month' => [
        'current_month' => '202401',
        'prev_month' => '202312',
        'next_month' => '202402'
    ],
    'timestamp' => 1570776103,
    'time_taken' => '0.076'
]
```
