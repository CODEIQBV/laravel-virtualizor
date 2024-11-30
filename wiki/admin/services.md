#### Service Management

```php
use CODEIQ\Virtualizor\VirtualizorAdmin;

// List services on master server
$services = VirtualizorAdmin::services()->list();

// List services on specific server
$services = VirtualizorAdmin::services()->list(3);

// Get raw API response
$response = VirtualizorAdmin::services()->list(raw: true);
```

Service List Response:
```php
[
    'services' => [
        'auditd',
        'cgconfig',
        'crond',
        'dhcpd',
        'ebtables',
        'haldaemon',
        'ip6tables',
        'iptables',
        // ...
    ],
    'autostart' => [
        'auditd',
        'blk-availability',
        'cgconfig',
        // ...
    ],
    'running' => [
        'auditd',
        'cgconfig',
        'crond',
        // ...
    ],
    'timestamp' => 1536668727,
    'time_taken' => '2.193'
]
```

// Manage services
$result = VirtualizorAdmin::services()->manage(
['xinetd'],           // Array of service names
'start',             // Action: start, stop, or restart
3                    // Optional: server ID (null for master server)
);

// Get raw API response
$response = VirtualizorAdmin::services()->manage(['xinetd'], 'start', raw: true);

// Restart a service
$success = VirtualizorAdmin::services()->restart(
'mysqld',            // Service name: webserver, network, sendmail, mysqld, iptables
3                    // Optional: server ID (null for master server)
);

// Get raw API response
$response = VirtualizorAdmin::services()->restart('mysqld', raw: true);
```
