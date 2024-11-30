#### Process Management

```php
use CODEIQ\Virtualizor\VirtualizorAdmin;

// List processes on master server
$processes = VirtualizorAdmin::processes()->list();

// List processes on specific server
$processes = VirtualizorAdmin::processes()->list(3);

// Get raw API response
$response = VirtualizorAdmin::processes()->list(raw: true);
```

Process List Response:
```php
[
    'processes' => [
        [
            'pid' => 1,
            'user' => 'root',
            'cpu' => 0.0,
            'memory' => [
                'percent' => 0.0,
                'rss' => 1100
            ],
            'tty' => '?',
            'state' => 'Ss',
            'time' => '00:00:01',
            'command' => '/sbin/init'
        ],
        [
            'pid' => 2,
            'user' => 'root',
            'cpu' => 0.0,
            'memory' => [
                'percent' => 0.0,
                'rss' => 0
            ],
            'tty' => '?',
            'state' => 'S',
            'time' => '00:00:00',
            'command' => '[kthreadd]'
        ]
    ],
    'timestamp' => 1471413592,
    'time_taken' => '0.158'
]
```

// Kill processes
$result = VirtualizorAdmin::processes()->kill(
[44, 54, 1239],      // Array of process IDs to kill
3                    // Optional: server ID (null for master server)
);

// Get raw API response
$response = VirtualizorAdmin::processes()->kill([44, 54, 1239], raw: true);
```
