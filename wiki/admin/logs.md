#### Log Management

```php
use CODEIQ\Virtualizor\VirtualizorAdmin;

// List all logs
$logs = VirtualizorAdmin::logs()->list();

// List with filters
$logs = VirtualizorAdmin::logs()->list([
    'id' => 3273,           // Filter by VPS ID
    'email' => 'root'       // Filter by email
]);

// List with pagination
$logs = VirtualizorAdmin::logs()->list([], 1, 10);

// Get raw API response
$response = VirtualizorAdmin::logs()->list(raw: true);

// List admin activity logs
$logs = VirtualizorAdmin::logs()->adminLogs();

// List with filters
$logs = VirtualizorAdmin::logs()->adminLogs([
    'id' => 3273,           // Filter by VPS ID
    'email' => 'root'       // Filter by email
]);

// List with pagination
$logs = VirtualizorAdmin::logs()->adminLogs([], 1, 10);

// Get raw API response
$response = VirtualizorAdmin::logs()->adminLogs(raw: true);

// List login logs
$logs = VirtualizorAdmin::logs()->loginLogs();

// List with filters
$logs = VirtualizorAdmin::logs()->loginLogs([
    'username' => 'root',    // Filter by username
    'ip' => '192.168.1.188' // Filter by IP address
]);

// List with pagination
$logs = VirtualizorAdmin::logs()->loginLogs([], 1, 10);

// Get raw API response
$response = VirtualizorAdmin::logs()->loginLogs(raw: true);

// List user activity logs
$logs = VirtualizorAdmin::logs()->userLogs();

// List with filters
$logs = VirtualizorAdmin::logs()->userLogs([
    'vpsid' => 3628,         // Filter by VPS ID
    'email' => 'user@example.com'  // Filter by email
]);

// List with pagination
$logs = VirtualizorAdmin::logs()->userLogs([], 1, 20);

// Get raw API response
$response = VirtualizorAdmin::logs()->userLogs(raw: true);
```

Admin Log Response:
```php
[
    'logs' => [
        [
            'id' => 7430,
            'user_id' => 0,
            'resource_id' => 3273,
            'action' => [
                'type' => 'editvs',
                'description' => 'Edit VPS'
            ],
            'data' => 'v1949',
            'status' => true,
            'ip' => '120.62.45.250',
            'email' => 'root',
            'timestamp' => 1471415822
        ]
    ],
    'timestamp' => 1471416157,
    'time_taken' => '0.10'
]
```

Login Log Response:
```php
[
    'logs' => [
        [
            'username' => 'root',
            'vps_id' => 0,
            'timestamp' => 1536668301,
            'status' => true,
            'ip' => '192.168.1.188'
        ]
    ],
    'timestamp' => 1536684609,
    'time_taken' => '0.210'
]
```

User Log Response:
```php
[
    'logs' => [
        [
            'vps_id' => 3628,
            'email' => 'user@example.com',
            'action' => [
                'type' => 'login',
                'description' => 'User logged in'
            ],
            'data' => 'v1949',
            'status' => true,
            'ip' => '120.62.45.250',
            'timestamp' => 1471415822
        ]
    ],
    'timestamp' => 1471416157,
    'time_taken' => '0.10'
]
```

// List IP logs
$logs = VirtualizorAdmin::logs()->ipLogs();

// List with filters
$logs = VirtualizorAdmin::logs()->ipLogs([
'ip' => '10.0.0.2',         // Filter by IP address
'vpsid' => 74               // Filter by VPS ID
]);

// List with pagination
$logs = VirtualizorAdmin::logs()->ipLogs([], 1, 20);

// Get raw API response
$response = VirtualizorAdmin::logs()->ipLogs(raw: true);

// Delete all IP logs
$success = VirtualizorAdmin::logs()->deleteIpLogs();

// Get raw API response
$response = VirtualizorAdmin::logs()->deleteIpLogs(raw: true);
```
