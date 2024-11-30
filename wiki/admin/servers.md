#### Server Management

```php
use CODEIQ\Virtualizor\VirtualizorAdmin;

// List all servers
$servers = VirtualizorAdmin::servers()->list();

// List servers with filters
$servers = VirtualizorAdmin::servers()->list([
    'servername' => 'web1',
    'serverip' => '192.168.1.100',
    'ptype' => 'kvm'  // openvz, xen, kvm, xcp, vzo, vzk, proxk, proxl, proxmo
]);

// List servers with pagination
$servers = VirtualizorAdmin::servers()->list([], 1, 10);

// Add new server
$server = VirtualizorAdmin::servers()->create([
    'server_name' => 'web-server-1',
    'ip' => '192.168.1.100',
    'pass' => 'slave-api-password',
    'sgid' => 0,  // Server Group ID
    'locked' => false,  // optional
    'internal_ip' => '10.0.0.100'  // optional
]);

// Get raw API response
$response = VirtualizorAdmin::servers()->create([...], raw: true);

// Edit server
$server = VirtualizorAdmin::servers()->update(1, [
    'server_name' => 'web-server-2',
    'ip' => '192.168.1.101',
    'pass' => 'new-api-password',  // optional
    'sgid' => 2,                   // optional
    'locked' => true,              // optional
    'internal_ip' => '10.0.0.101'  // optional
]);

// Get raw API response
$response = VirtualizorAdmin::servers()->update(1, [...], raw: true);

// Delete server
$result = VirtualizorAdmin::servers()->delete(2);

// Get raw API response
$response = VirtualizorAdmin::servers()->delete(2, raw: true);

// Get server loads
$loads = VirtualizorAdmin::servers()->loads();

// Get raw API response
$response = VirtualizorAdmin::servers()->loads(raw: true);
```

Server List Response Format:
```php
[
    [
        'serid' => '1',
        'server_name' => 'web-server-1',
        'ip' => '192.168.1.100',
        'virt' => 'kvm',
        'total_ram' => '1989',
        'ram' => '1038',
        'total_space' => '18',
        'space' => '5',
        'status' => '1',
        // ... other server properties
    ],
    // ... more servers
]
```

Create Server Response:
```php
[
    'success' => true,
    'id' => 6,
    'name' => 'web-server-1',
    'ip' => '192.168.1.100',
    'server_groups' => [
        // server groups data
    ]
]
```

Raw API Response:
```php
[
    'title' => 'Add Server',
    'saved' => 6,  // Server ID
    'servergroups' => [
        // server groups data
    ],
    'timenow' => '1474850356',
    'time_taken' => '1.827'
]
```

Update Server Response:
```php
[
    'success' => true,
    'id' => 1,
    'server' => [
        'serid' => '1',
        'server_name' => 'web-server-2',
        'ip' => '192.168.1.101',
        'virt' => 'kvm',
        // ... other server properties
    ],
    'server_groups' => [
        // server groups data
    ]
]
```

Raw API Response:
```php
[
    'title' => 'Edit Server',
    'saved' => true,
    'serid' => 1,
    'serv' => [
        // server data
    ],
    'servergroups' => [
        // server groups data
    ],
    'timenow' => 1537448594,
    'time_taken' => '0.352'
]
```

Delete Server Response:
```php
[
    'success' => true,
    'id' => 2,
    'servers' => [
        // remaining servers list
    ]
]
```

Raw API Response:
```php
[
    'title' => 'Servers',
    'servs' => [
        // remaining servers list
    ],
    'servers' => [
        // duplicate of servs
    ],
    'timenow' => '1474852126',
    'time_taken' => '0.693'
]
```

Server Loads Response:
```php
[
    'loads' => [
        10 => [  // VPS ID
            '1' => '0.00',  // 1 minute load
            '5' => '0.00',  // 5 minute load
            '15' => '0.00'  // 15 minute load
        ]
    ],
    'timestamp' => 1563257184
]
```

Raw API Response:
```php
[
    'title' => 'VPS Loads',
    'vpsusage' => [
        10 => [
            '1' => ' 0.00',
            '5' => ' 0.00',
            '15' => ' 0.00'
        ]
    ],
    'timenow' => 1563257184,
    'time_taken' => '0.110'
]
```
