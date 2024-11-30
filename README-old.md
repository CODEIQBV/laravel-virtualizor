# Laravel Virtualizor

[![Latest Version on Packagist](https://img.shields.io/packagist/v/codeiqbv/laravel-virtualizor.svg?style=flat-square)](https://packagist.org/packages/codeiqbv/laravel-virtualizor)
[![Total Downloads](https://img.shields.io/packagist/dt/codeiqbv/laravel-virtualizor.svg?style=flat-square)](https://packagist.org/packages/codeiqbv/laravel-virtualizor)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

A Laravel package that provides a clean and elegant way to interact with the Virtualizor API. This package offers a fluent interface for both Admin and Enduser APIs, making it easy to manage your Virtualizor infrastructure from your Laravel application.

## Features

- 🚀 Full support for both Admin and Enduser APIs
- 💪 Fluent interface with proper type hinting
- 🛡️ Comprehensive error handling
- 📝 Extensive documentation
- ⚡ Laravel integration with config file and facade
- 🧪 Well tested

## Requirements

- PHP 8.0 or higher
- Laravel 10.0 or higher

## Installation

You can install the package via composer:

```bash
composer require codeiqbv/laravel-virtualizor
```

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag="virtualizor-config"
```

Add your credentials to `.env`:

```env
VIRTUALIZOR_ADMIN_KEY=your_admin_key
VIRTUALIZOR_ADMIN_PASS=your_admin_pass
VIRTUALIZOR_ADMIN_IP=your_admin_ip
VIRTUALIZOR_ADMIN_PORT=4085

VIRTUALIZOR_ENDUSER_KEY=your_enduser_key
VIRTUALIZOR_ENDUSER_PASS=your_enduser_pass
VIRTUALIZOR_ENDUSER_IP=your_enduser_ip
VIRTUALIZOR_ENDUSER_PORT=4083

VIRTUALIZOR_DEBUG=false
```

## Usage

### Admin API



## Error Handling

The package throws `VirtualizorApiException` for API errors:

```php
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

try {
    $users = VirtualizorAdmin::users()->list();
} catch (VirtualizorApiException $e) {
    $message = $e->getMessage();
    $context = $e->getContext(); // Available when debug is enabled
}
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.



#### Disk Management

```php
use CODEIQ\Virtualizor\VirtualizorAdmin;

// Get disk info from master server
$diskInfo = VirtualizorAdmin::disk()->info();

// Get disk info from specific server
$diskInfo = VirtualizorAdmin::disk()->info(1);

// Get raw API response
$response = VirtualizorAdmin::disk()->info(raw: true);
```

Disk Info Response:
```php
[
    '/' => [
        'size' => [
            'total' => [
                'mb' => 9714.86,
                'gb' => 9.49
            ],
            'used' => [
                'mb' => 3473.91,
                'gb' => 3.39,
                'actual_gb' => 3.88
            ],
            'available' => [
                'mb' => 5740.94,
                'gb' => 6.09
            ],
            'free' => [
                'mb' => 6240.94
            ]
        ],
        'usage' => [
            'percent' => 35.76,
            'percent_free' => 64.24
        ]
    ],
    '/boot' => [
        'size' => [
            'total' => [
                'mb' => 239.92,
                'gb' => 0.23
            ],
            'used' => [
                'mb' => 48.34,
                'gb' => 0.05,
                'actual_gb' => 0.06
            ],
            'available' => [
                'mb' => 178.78,
                'gb' => 0.19
            ],
            'free' => [
                'mb' => 191.58
            ]
        ],
        'usage' => [
            'percent' => 20.15,
            'percent_free' => 79.85
        ]
    ]
]
```

#### Bandwidth Management

```php
use CODEIQ\Virtualizor\VirtualizorAdmin;

// Get bandwidth usage for specific month
$usage = VirtualizorAdmin::bandwidth()->usage('202401');

// Get raw API response
$response = VirtualizorAdmin::bandwidth()->usage('202401', raw: true);
```

Bandwidth Usage Response:
```php
[
    'bandwidth' => [
        'total' => [
            'limit' => [
                'mb' => 102400.0,
                'gb' => 100.0
            ],
            'used' => [
                'mb' => 46647.12,
                'gb' => 45.55
            ],
            'free' => [
                'mb' => 55752.88,
                'gb' => 54.45
            ],
            'percent' => [
                'used' => 45.55,
                'free' => 54.45
            ]
        ],
        'daily_usage' => [
            '1' => 205.78,
            '2' => 2160.2,
            // ... daily usage for each day
        ],
        'incoming' => [
            'usage' => [
                '1' => 79.1,
                '2' => 378.38,
                // ... daily incoming usage
            ],
            'total' => [
                'limit' => [
                    'mb' => 16732.83,
                    'gb' => 16.34
                ],
                'used' => [
                    'mb' => 16731.83,
                    'gb' => 16.34
                ],
                'free' => [
                    'mb' => 1.0,
                    'gb' => 0.001
                ],
                'percent' => [
                    'used' => 99.99,
                    'free' => 0.01
                ]
            ]
        ],
        'outgoing' => [
            'usage' => [
                '1' => 126.68,
                '2' => 1781.82,
                // ... daily outgoing usage
            ],
            'total' => [
                'limit' => [
                    'mb' => 29916.29,
                    'gb' => 29.22
                ],
                'used' => [
                    'mb' => 29915.29,
                    'gb' => 29.21
                ],
                'free' => [
                    'mb' => 1.0,
                    'gb' => 0.001
                ],
                'percent' => [
                    'used' => 99.99,
                    'free' => 0.01
                ]
            ]
        ]
    ],
    'month' => [
        'yr' => '2016',
        'month' => '11',
        'mth_txt' => 'Nov',
        'days' => '30',
        'prev' => '201610',
        'next' => '201612'
    ],
    'timestamp' => 1471411017,
    'time_taken' => '0.104'
]
```

#### Firewall Management

```php
use CODEIQ\Virtualizor\VirtualizorAdmin;

// Get firewall status
$isRunning = VirtualizorAdmin::firewall()->status();

// Start firewall
$success = VirtualizorAdmin::firewall()->start();

// Stop firewall
$success = VirtualizorAdmin::firewall()->stop();

// Restart firewall
$success = VirtualizorAdmin::firewall()->restart();

// Block ports
$success = VirtualizorAdmin::firewall()->blockPort(80);              // Single port
$success = VirtualizorAdmin::firewall()->blockPort([80, 443]);       // Multiple ports

// Allow ports
$success = VirtualizorAdmin::firewall()->allowPort(80);              // Single port
$success = VirtualizorAdmin::firewall()->allowPort([80, 443]);       // Multiple ports

// Block IP address
$success = VirtualizorAdmin::firewall()->blockIp('192.168.1.100');                    // All ports
$success = VirtualizorAdmin::firewall()->blockIp('192.168.1.100', [80, 443]);         // Specific ports
$success = VirtualizorAdmin::firewall()->blockIp('192.168.1.100', null, true);        // Temporary rule

// Allow IP address
$success = VirtualizorAdmin::firewall()->allowIp('192.168.1.100');                    // All ports
$success = VirtualizorAdmin::firewall()->allowIp('192.168.1.100', [80, 443]);         // Specific ports
$success = VirtualizorAdmin::firewall()->allowIp('192.168.1.100', null, true);        // Temporary rule

// Search IP rules
$rules = VirtualizorAdmin::firewall()->searchIp('192.168.1.100');

// View all rules
$rules = VirtualizorAdmin::firewall()->viewRules();

// Toggle testing mode
$success = VirtualizorAdmin::firewall()->toggleTestingMode(true);    // Enable
$success = VirtualizorAdmin::firewall()->toggleTestingMode(false);   // Disable

// Get raw API responses
$response = VirtualizorAdmin::firewall()->status(raw: true);
$response = VirtualizorAdmin::firewall()->blockPort(80, raw: true);
$response = VirtualizorAdmin::firewall()->blockIp('192.168.1.100', raw: true);
// etc...
```

Firewall Status Response:
```php
[
    'output' => [
        'iptables: Firewall is running.'
    ],
    'timestamp' => 1471411564,
    'time_taken' => '0.113'
]
```

Firewall Rules Response:
```php
[
    'output' => [
        'Chain INPUT (policy ACCEPT)',
        'target     prot opt source               destination',
        'DROP       tcp  --  192.168.1.100        anywhere            tcp dpt:http',
        'ACCEPT     tcp  --  192.168.1.101        anywhere            tcp dpt:https',
        // ...
    ],
    'timestamp' => 1471411564,
    'time_taken' => '0.113'
]
```

#### Server Monitoring

```php
use CODEIQ\Virtualizor\VirtualizorAdmin;

// Get default server information
$info = VirtualizorAdmin::monitoring()->get(3);

// Get live statistics
$stats = VirtualizorAdmin::monitoring()->get(3, 'live_stats');

// Get network statistics
$network = VirtualizorAdmin::monitoring()->get(3, 'network_stats');

// Get raw API response
$response = VirtualizorAdmin::monitoring()->get(3, raw: true);
```

Default Mode Response:
```php
[
    'hardware' => [
        'disks' => [...],           // Available disks
        'ram' => [...],             // RAM specifications
        'cpu' => [...],             // CPU specifications
        'partitions' => [
            'space' => [...],       // Partition space usage
            'inodes' => [...]       // Partition inode usage
        ]
    ],
    'timestamp' => 1645002977,
    'time_taken' => '1.066'
]
```

Live Stats Response:
```php
[
    'performance' => [
        'cpu' => [
            'model' => 'Intel(R) Xeon(R) CPU E3-1220 v6 @ 3.00GHz',
            'usage' => 6.82
        ],
        'ram' => [
            'usage' => 55.06
        ],
        'disk' => [
            'health' => [...],
            'io' => [
                'sda' => [
                    'utilization' => 6.38,
                    'read_speed' => 0.07,
                    'write_speed' => 0.11,
                    'reads' => 1286823,
                    'writes' => 2184056
                ]
            ]
        ],
        'processes' => [
            'top_cpu' => [
                [
                    'pid' => 27370,
                    'ppid' => 1,
                    'command' => '/usr/libexec/qemu-kvm -name',
                    'cpu' => 11.2,
                    'ram' => 16.7
                ]
            ],
            'top_ram' => [...]
        ],
        'running_scripts' => [...],
        'missing_binaries' => ['/sbin/gdisk']
    ],
    'timestamp' => 1645002977,
    'time_taken' => '1.066'
]
```

Network Stats Response:
```php
[
    'network' => [
        'interfaces' => [
            'eth0' => [
                'up_speed' => 1000,
                'down_speed' => 1000
            ]
        ]
    ],
    'timestamp' => 1645002977,
    'time_taken' => '1.066'
]
```

#### SSH Key Management

```php
use CODEIQ\Virtualizor\VirtualizorAdmin;

// List SSH keys for a user
$keys = VirtualizorAdmin::sshKeys()->list(3582);

// Get raw API response
$response = VirtualizorAdmin::sshKeys()->list(3582, raw: true);

// Add SSH keys to a VPS
$success = VirtualizorAdmin::sshKeys()->addToVps(
    1234,           // VPS ID
    [14, 15, 16]    // Array of SSH key IDs to add
);

// Get raw API response
$response = VirtualizorAdmin::sshKeys()->addToVps(1234, [14, 15, 16], raw: true);
```

SSH Key List Response:
```php
[
    'keys' => [
        [
            'id' => 14,
            'name' => 'ssh_key_auto_spjpgk5nsiohvxz9',
            'value' => 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQC7IqAlA86ZUABy0TSCc... root@lxc'
        ]
    ],
    'timestamp' => 1471411564,
    'time_taken' => '0.113'
]
```

// Generate new SSH key pair
$keys = VirtualizorAdmin::sshKeys()->generate();

// Get raw API response
$response = VirtualizorAdmin::sshKeys()->generate(raw: true);
```

#### RAM Management

```php
use CODEIQ\Virtualizor\VirtualizorAdmin;

// Get RAM info from master server
$ramInfo = VirtualizorAdmin::ram()->info();

// Get RAM info from specific server
$ramInfo = VirtualizorAdmin::ram()->info(1);

// Get raw API response
$response = VirtualizorAdmin::ram()->info(raw: true);
```

RAM Info Response:
```php
[
    'physical' => [
        'total' => [
            'mb' => 1989
        ],
        'used' => [
            'mb' => 808
        ],
        'free' => [
            'mb' => 1181
        ],
        'usage' => [
            'percent' => 40.62,
            'percent_free' => 59.38
        ]
    ],
    'swap' => [
        'total' => [
            'mb' => 1999
        ],
        'used' => [
            'mb' => 1
        ],
        'free' => [
            'mb' => 1998
        ]
    ]
]
```

#### CPU Management

```php
use CODEIQ\Virtualizor\VirtualizorAdmin;

// Get CPU info from master server
$cpuInfo = VirtualizorAdmin::cpu()->info();

// Get CPU info from specific server
$cpuInfo = VirtualizorAdmin::cpu()->info(1);

// Get raw API response
$response = VirtualizorAdmin::cpu()->info(raw: true);
```

CPU Info Response:
```php
[
    'manufacturer' => 'intel',
    'model' => 'Intel(R) Pentium(R) CPU G620 @ 2.60GHz',
    'specs' => [
        'limit' => [
            'mhz' => 2600.0
        ],
        'used' => [
            'mhz' => 115.64
        ],
        'free' => [
            'mhz' => 2484.36
        ]
    ],
    'usage' => [
        'percent' => 4.45,
        'percent_free' => 95.55
    ]
]
```

#### Task Management

```php
use CODEIQ\Virtualizor\VirtualizorAdmin;

// Show all tasks
$tasks = VirtualizorAdmin::tasks()->show();

// Get raw API response
$response = VirtualizorAdmin::tasks()->show(raw: true);

// Show task logs
$logs = VirtualizorAdmin::tasks()->showLogs(484);

// Get raw API response
$response = VirtualizorAdmin::tasks()->showLogs(484, raw: true);
```

Task List Response:
```php
[
    'tasks' => [
        [
            'id' => 484,
            'slave_id' => 0,
            'user' => [
                'id' => 0,
                'email' => 'root',
                'ip' => '192.168.1.188'
            ],
            'vps' => [
                'id' => 0
            ],
            'server' => [
                'id' => 0,
                'name' => 'localhost'
            ],
            'action' => [
                'type' => 'getos',
                'description' => 'Downloading OS',
                'text' => 'Downloading OS'
            ],
            'status' => [
                'code' => 1,
                'text' => 'Task Completed',
                'progress' => 100
            ],
            'timing' => [
                'started' => 'September 4, 2018, 5:12 pm',
                'updated' => 'September 4, 2018, 5:13 pm',
                'ended' => 'September 4, 2018, 5:13 pm',
                'timestamp' => 1536081131
            ],
            'process_id' => 0,
            'is_internal' => false,
            'data' => [
                'os' => [
                    [
                        'osid' => 100008,
                        'inprogress' => 0,
                        'done' => 1,
                        'failed' => 0,
                        'err_msg' => 'Download successful'
                    ]
                ]
            ]
        ]
    ],
    'logs' => null,
    'logs_info' => 'The logs shown are the last operation performed for this task by virtualizor',
    'timestamp' => 1536144695,
    'time_taken' => '0.361'
]
```

Task Logs Response:
```php
[
    'logs' => [
        [
            'category' => 'Download_OS',
            'message' => 'Starting Task : 484\nOS to Fetch...'
        ]
    ],
    'info' => 'The logs shown are the last operation performed for this task by virtualizor',
    'timestamp' => 1536149460,
    'time_taken' => '0.265'
]
```

// Search tasks
$tasks = VirtualizorAdmin::tasks()->search([
    'actid' => 484,           // Filter by task ID
    'vpsid' => 123,           // Filter by VPS ID
    'username' => 'admin',    // Filter by username
    'action' => 'addvs',      // Filter by action type
    'status' => 1,            // Filter by status (1=In Progress, 2=Completed, 3=Updated, -1=Errored)
    'order' => 'DESC'         // Sort order (ASC/DESC)
]);

// Search with pagination
$tasks = VirtualizorAdmin::tasks()->search([], 1, 20);

// Get raw API response
$response = VirtualizorAdmin::tasks()->search($filters, raw: true);
```

#### Node Performance

```php
use CODEIQ\Virtualizor\VirtualizorAdmin;

// Get current node performance stats
$stats = VirtualizorAdmin::nodePerformance()->stats();

// Get stats for specific month
$stats = VirtualizorAdmin::nodePerformance()->stats([
    'show' => '202401'    // YYYYMM format
]);

// Get stats for specific server
$stats = VirtualizorAdmin::nodePerformance()->stats([
    'serid' => 3
]);

// Get raw API response
$response = VirtualizorAdmin::nodePerformance()->stats(raw: true);
```

Node Performance Response:
```php
[
    'stats' => [
        [
            'server_id' => 0,
            'timestamp' => 1471403402,
            'resources' => [
                'disk' => 1.0,
                'inode' => 98835,
                'ram' => 253756,
                'cpu' => [
                    'usage' => 10796.0,
                    'actual' => 92.75
                ],
                'network' => [
                    'in' => 128,
                    'out' => 0
                ]
            ]
        ]
    ],
    'month' => [
        'current_month' => '202401',
        'prev_month' => '202312',
        'next_month' => '202402'
    ],
    'timestamp' => 1471403773,
    'time_taken' => '0.130'
]
```

#### SSL Management

```php
use CODEIQ\Virtualizor\VirtualizorAdmin;

// Get SSL files configuration
$sslFiles = VirtualizorAdmin::ssl()->files();

// Get raw API response
$response = VirtualizorAdmin::ssl()->files(raw: true);
```

SSL Files Response:
```php
[
    'key' => 'Key configuration',
    'certificate' => 'Certificate configuration',
    'csr' => 'Certificate Request Configuration',
    'bundle' => '',
    'timestamp' => 1471399565,
    'time_taken' => '0.103'
]
```

// Create SSL certificate
$success = VirtualizorAdmin::ssl()->create([
    'country' => 'US',           // 2-letter country code
    'state' => 'California',     // State/province
    'locality' => 'San Francisco', // City
    'organisation' => 'Company', // Organization name
    'comname' => 'example.com',  // Common name (domain)
    'email' => 'admin@example.com',
    'keysize' => 2048,          // Key size (1024, 2048, 4096)
    'orgunit' => 'IT'           // Optional: Organization unit
]);

// Get raw API response
$response = VirtualizorAdmin::ssl()->create($params, raw: true);
```

// Install Let's Encrypt certificate
$success = VirtualizorAdmin::ssl()->installLetsEncrypt([
    'primary_domain' => 'example.com',     // Domain name
    'contact_email' => 'admin@example.com',
    'key_size' => 2048,                    // 2048, 3072, 4096, 8192, ec-256, ec-384
    'renew_days' => 80,                    // Days before expiry to renew (1-89)
    'staging' => false,                    // Optional: Use staging environment
    'enable_force' => true,                // Optional: Force reinstall
    'ssl_type' => 'letsencrypt'           // Optional: letsencrypt or zerossl
]);

// Get raw API response
$response = VirtualizorAdmin::ssl()->installLetsEncrypt($params, raw: true);
```

// Renew Let's Encrypt certificate
$taskId = VirtualizorAdmin::ssl()->renewLetsEncrypt();

// Get raw API response
$response = VirtualizorAdmin::ssl()->renewLetsEncrypt(raw: true);
```

// Show Let's Encrypt logs
$logs = VirtualizorAdmin::ssl()->showLetsEncryptLogs();

// Get raw API response
$response = VirtualizorAdmin::ssl()->showLetsEncryptLogs(raw: true);
```

Let's Encrypt Logs Response:
```php
[
    'config' => [
        'domain' => 'hostname',
        'email' => 'specified_email',
        'key_size' => 2048,
        'renew_days' => 80,
        'staging' => true,
        'force_enabled' => false
    ],
    'certificate' => [
        'domain' => 'hostname',
        'san' => 'DNS:hostname',
        'issuer' => 'Let\'s Encrypt Authority X3',
        'serial' => 'CERTSERIALNO',
        'validity' => [
            'from' => 'Sat, 12 Nov 2016 08:36:00 GMT',
            'to' => 'Fri, 10 Feb 2017 08:36:00 GMT',
            'next_renewal' => 'Tue, 31 Jan 2017 09:35:52 GMT'
        ],
        'is_installed' => true
    ],
    'logs' => '3677\n[Wed Nov 16 07:14:26 GMT 2016] Verifying information...',
    'task_id' => null,
    'timestamp' => 1480504710,
    'time_taken' => '0.560'
]
```

// Configure database backups
$success = VirtualizorAdmin::backup()->configureDatabaseBackups([
    'enabled' => true,                // Enable/disable backups
    'type' => 'EMAIL',               // EMAIL, SSH, or FTP
    'cron' => '0 0 * * *',          // Cron schedule
    'email' => 'admin@example.com'   // Required for EMAIL type
]);

// Configure SSH/FTP backups
$success = VirtualizorAdmin::backup()->configureDatabaseBackups([
    'enabled' => true,
    'type' => 'SSH',                 // or 'FTP'
    'cron' => '0 0 * * *',
    'server_id' => 1,                // Backup server ID
    'server_dir' => '/backups'       // Backup directory
]);

// Disable database backups
$success = VirtualizorAdmin::backup()->disableDatabaseBackups();

// Get raw API response
$response = VirtualizorAdmin::backup()->configureDatabaseBackups($params, raw: true);
```

Database Backup Configuration Response:
```php
[
    'title' => 'Database Backup',
    'done' => [
        'cron_set' => 1
    ],
    'backup_servers' => [
        '1' => [
            'bid' => '1',
            'name' => 'slate_doc_server',
            'type' => 'SSH'
        ]
    ],
    'timestamp' => 1536255440,
    'time_taken' => '0.214'
]
```

Database Backup Disable Response:
```php
[
    'title' => 'Database Backup',
    'backup_servers' => [
        '1' => [
            'bid' => '1',
            'name' => 'slate_doc_server',
            'type' => 'SSH'
        ]
    ],
    'timestamp' => 1537286069,
    'time_taken' => '0.268'
]
```

// List database backups
$backups = VirtualizorAdmin::backup()->listDatabaseBackups();

// Get raw API response
$response = VirtualizorAdmin::backup()->listDatabaseBackups(raw: true);
```

Database Backup List Response:
```php
[
    'backups' => [
        [
            'id' => 2,
            'filename' => 'virtualizor-2018-09-18_16.33.13.sql.gz',
            'timestamp' => 1537284793
        ],
        [
            'id' => 3,
            'filename' => 'virtualizor-2018-09-18_16.33.18.sql.gz',
            'timestamp' => 1537284798
        ]
    ],
    'timestamp' => 1537284800,
    'time_taken' => '0.103'
]
```

// Delete database backup(s)
$success = VirtualizorAdmin::backup()->deleteDatabaseBackup('20160529.sql');

// Delete multiple backups
$success = VirtualizorAdmin::backup()->deleteDatabaseBackup([
    '20160529.sql',
    '20160530.sql'
]);

// Get raw API response
$response = VirtualizorAdmin::backup()->deleteDatabaseBackup($backupIds, raw: true);
```

Database Backup Delete Response:
```php
[
    'title' => 'Database Backup',
    'done' => [
        'delete' => true,
        'db_no_avi' => true
    ],
    'backup_servers' => [
        '1' => [
            'bid' => '1',
            'name' => 'slate_doc_server',
            'type' => 'SSH'
        ]
    ],
    'timestamp' => 1536259307,
    'time_taken' => '0.201'
]
```

// Create database backup
$success = VirtualizorAdmin::backup()->createDatabaseBackup();

// Get raw API response
$response = VirtualizorAdmin::backup()->createDatabaseBackup(raw: true);
```

Database Backup Create Response:
```php
[
    'title' => 'Database Backup',
    'done' => [
        'succ' => true
    ],
    'filename' => [
        '2' => 'virtualizor-2018-09-18_16.33.18.sql.gz',
        '3' => 'virtualizor-2018-09-18_17.23.28.sql.gz',
        '4' => 'virtualizor-2018-09-18_17.29.19.sql.gz',
        '5' => 'virtualizor-2018-09-18_17.37.15.sql.gz'
    ],
    'backup_servers' => [
        '1' => [
            'bid' => '1',
            'name' => 'slate_doc_server',
            'type' => 'SSH'
        ]
    ],
    'timestamp' => 1537288640,
    'time_taken' => '0.223'
]
```

// Create VPS backup
$success = VirtualizorAdmin::backup()->createVpsBackup(3);

// Get raw API response
$response = VirtualizorAdmin::backup()->createVpsBackup(3, raw: true);
```

VPS Backup Create Response:
```php
[
    'title' => 'Edit Backup Plan',
    'done' => 1,
    'backup_plan' => [
        'bpid' => '3',
        'disabled' => '0',
        'plan_name' => 'api backup',
        'bid' => '0',
        'frequency' => 'hourly',
        'run_time' => '00:00',
        'hourly_freq' => '1',
        'run_day' => '1',
        'run_date' => '1',
        'rotation' => '1',
        'backup_limit' => '0',
        'restore_limit' => '0',
        'enable_enduser_backup_servers' => '0',
        'nice' => '0',
        'ionice_prio' => '0',
        'ionice_class' => '3',
        'disable_compression' => '1',
        'dir' => '/tmp/api_backup'
    ],
    'vpses' => [
        '79' => [
            'vpsid' => '79',
            'vps_name' => 'v1002',
            'serid' => '0',
            'hostname' => 'www.mydomainpp.com',
            'space' => '2'
        ]
    ],
    'timestamp' => 1537290773,
    'time_taken' => '0.269'
]
```

// Get VPS backup details
$backups = VirtualizorAdmin::backup()->getVpsBackupDetails(79);

// Get backups for specific date
$backups = VirtualizorAdmin::backup()->getVpsBackupDetails(79, '20240101');

// Get raw API response
$response = VirtualizorAdmin::backup()->getVpsBackupDetails(79, raw: true);
```

VPS Backup Details Response:
```php
[
    'backups' => [
        '20180918' => [
            [
                'path' => '/tmp/api_backup/20180918/79.img',
                'size' => [
                    'bytes' => 2147483648,
                    'mb' => 2048.0,
                    'gb' => 2.0
                ]
            ]
        ]
    ],
    'directories' => [
        '/tmp/api_backup'
    ],
    'server' => [
        'id' => 0,
        'directory' => '/tmp/api_backup'
    ],
    'timestamp' => 1537293793,
    'time_taken' => '0.223'
]
```

// Restore VPS backup
$success = VirtualizorAdmin::backup()->restoreVpsBackup([
    'vpsid' => 79,                    // VPS ID to restore
    'dir' => '/tmp/api_backup',       // Backup directory
    'date' => '20240101',             // Backup date (YYYYMMDD)
    'file' => 'backup.tar.gz',        // Backup filename
    'bid' => 1,                       // Optional: Backup server ID
    'newvps' => true,                 // Optional: Restore to new VPS
    'newserid' => 2                   // Optional: Server ID for new VPS
]);

// Get raw API response
$response = VirtualizorAdmin::backup()->restoreVpsBackup($params, raw: true);
```

VPS Backup Restore Response:
```php
[
    'title' => 'Restore VPS Backups',
    'restore_done' => true,
    'restore_details' => '',
    'timestamp' => 1537294345,
    'time_taken' => '0.244'
]
```

// Delete VPS backup
$success = VirtualizorAdmin::backup()->deleteVpsBackup([
    'file' => '79.img',              // Backup filename
    'dir' => '/tmp/api_backup',      // Backup directory
    'date' => '20240101',            // Backup date (YYYYMMDD)
    'bid' => 1                       // Optional: Backup server ID
]);

// Get raw API response
$response = VirtualizorAdmin::backup()->deleteVpsBackup($params, raw: true);
```

VPS Backup Delete Response:
```php
[
    'title' => 'Restore VPS Backups',
    'delete_done' => true,
    'timestamp' => 1537296233,
    'time_taken' => '0.285'
]
```

// Create single VPS backup
$taskId = VirtualizorAdmin::backup()->createSingleVpsBackup(3);

// Get raw API response
$response = VirtualizorAdmin::backup()->createSingleVpsBackup(3, raw: true);
```

Single VPS Backup Response:
```php
[
    'title' => 'Manage VPS',
    'done' => [
        'msg' => 'The VPS backup was started successfully'
    ],
    'actid' => '31794',
    'timestamp' => 1537290773,
    'time_taken' => '0.269'
]
```

#### Configuration Management

```php
use CODEIQ\Virtualizor\VirtualizorAdmin;

// Get master settings
$settings = VirtualizorAdmin::config()->getMasterSettings();

// Get raw API response
$response = VirtualizorAdmin::config()->getMasterSettings(raw: true);
```

Master Settings Response:
```php
[
    'globals' => [
        'path' => '/usr/local/virtualizor',
        'slave' => 0,
        'kernel' => 'kvm',
        'language' => 'english',
        'timezone' => '0',
        // ... more global settings
    ],
    'info' => [
        // Server information
    ],
    'languages' => [
        'english' => 'english',
        'french' => 'french',
        // ... available languages
    ],
    'themes' => [
        'default' => 'default'
    ],
    'timestamp' => 1536163383,
    'time_taken' => '0.233'
]
```

// Update master settings
$success = VirtualizorAdmin::config()->updateMasterSettings([
    'sn' => 'API Virt',                // Server/company name
    'sess_len' => 30000,               // Session timeout (seconds)
    'soft_email' => 'admin@example.com', // From email address
    'language' => 'english',           // Interface language
    'timezone' => 0,                   // Server timezone
    'theme_folder' => 'default',       // Theme name
    'admin_logs' => 180,               // Log retention days
    'login_attempts' => 5,             // Max login attempts
    'login_ban_time' => 15,            // Ban duration (minutes)
    // ... many more options available
]);

// Get raw API response
$response = VirtualizorAdmin::config()->updateMasterSettings($settings, raw: true);
```

Master Settings Update Response:
```php
[
    'title' => 'Configuration',
    'done' => 1,
    'globals' => [
        // Updated configuration
    ],
    'info' => [
        // Server information
    ],
    'timestamp' => 1536234874,
    'time_taken' => '0.288'
]
```

// Get slave settings
$settings = VirtualizorAdmin::config()->getSlaveSettings();

// Get raw API response
$response = VirtualizorAdmin::config()->getSlaveSettings(raw: true);
```

Slave Settings Response:
```php
[
    'globals' => [
        'path' => '/usr/local/virtualizor',
        'slave' => 0,
        'kernel' => 'kvm',
        'language' => 'english',
        'timezone' => '0',
        // ... more global settings
    ],
    'info' => [
        // Server information
    ],
    'languages' => [
        'english' => 'english',
        'french' => 'french',
        // ... available languages
    ],
    'themes' => [
        'default' => 'default'
    ],
    'timestamp' => 1536235460,
    'time_taken' => '0.252'
]
```

// Update slave settings
$success = VirtualizorAdmin::config()->updateSlaveSettings([
    'serid' => 1,                     // Server ID
    'cookie_name' => 'SIMCookies3042', // Cookie name
    'soft_email' => 'admin@example.com', // From email address
    'timezone' => 0,                   // Server timezone
    'cron_time' => '18 16 * * 3',     // VPS info cron schedule
    'emps_cron_time' => '18 16 * * 3', // EMPS update cron schedule
    'cpu_nm' => true,                 // Show CPU model name
    'overcommit' => 0,                // RAM overcommit ratio
    'vnc_ip' => '192.168.1.100',      // VNC IP address
    'node_bandwidth' => 1000,         // Node bandwidth (GB)
    'vpslimit' => 100,                // Max VPS count
    'vcores' => 8,                    // Virtual cores available
    'haproxy_enable' => true,         // Enable HAProxy
    // ... many more options available
]);

// Get raw API response
$response = VirtualizorAdmin::config()->updateSlaveSettings($settings, raw: true);
```

Slave Settings Update Response:
```php
[
    'title' => 'Configuration',
    'done' => 1,
    'globals' => [
        // Updated configuration
    ],
    'info' => [
        // Server information
    ],
    'timestamp' => 1536236494,
    'time_taken' => '0.240'
]
```

// Get Webuzo settings
$settings = VirtualizorAdmin::config()->getWebuzoSettings();

// Update Webuzo settings
$success = VirtualizorAdmin::config()->updateWebuzoSettings([
    'noc_apikey' => 'your-api-key',      // NOC API key
    'noc_apipass' => 'your-api-pass',    // NOC API password
    'disable_webuzo' => false,           // Disable all Webuzo scripts
    'select_all' => false,               // Select all scripts
    'sel_scripts' => [72, 79, 13]        // Array of script IDs to enable
]);

// Get raw API response
$response = VirtualizorAdmin::config()->getWebuzoSettings(raw: true);
```

Webuzo Settings Response:
```php
[
    'scripts' => [
        [
            'id' => 26,
            'parent_id' => 0,
            'name' => 'WordPress',
            'soft_name' => 'wp',
            'type' => 'php',
            'category' => 'blogs'
        ]
    ],
    'enabled_scripts' => [72, 79, 13],
    'timestamp' => 1537180879,
    'time_taken' => '0.109'
]
```

// Get Webuzo scripts
$scripts = VirtualizorAdmin::config()->getWebuzoScripts();

// Get raw API response
$response = VirtualizorAdmin::config()->getWebuzoScripts(raw: true);
```

Webuzo Scripts Response:
```php
[
    'scripts' => [
        [
            'id' => 26,
            'parent_id' => 0,
            'name' => 'WordPress',
            'soft_name' => 'wp',
            'type' => 'php',
            'category' => 'blogs'
        ],
        [
            'id' => 72,
            'parent_id' => 0,
            'name' => 'PrestaShop',
            'soft_name' => 'presta',
            'type' => 'php',
            'category' => 'ecommerce'
        ]
    ],
    'by_category' => [
        'blogs' => [
            [
                'id' => 26,
                'parent_id' => 0,
                'name' => 'WordPress',
                'soft_name' => 'wp',
                'type' => 'php',
                'category' => 'blogs'
            ]
        ],
        'ecommerce' => [
            [
                'id' => 72,
                'parent_id' => 0,
                'name' => 'PrestaShop',
                'soft_name' => 'presta',
                'type' => 'php',
                'category' => 'ecommerce'
            ]
        ]
    ],
    'timestamp' => 1537180879,
    'time_taken' => '0.109'
]
```

// Get email settings
$settings = VirtualizorAdmin::config()->getEmailSettings();

// Update email settings using PHP mail
$success = VirtualizorAdmin::config()->updateEmailSettings([
    'use_php_mail' => true,
    'disable_emails' => false
]);

// Update email settings using SMTP
$success = VirtualizorAdmin::config()->updateEmailSettings([
    'use_php_mail' => false,
    'server' => 'smtp.example.com',
    'port' => 587,
    'username' => 'user@example.com',
    'password' => 'password',
    'smtp_security' => 2,         // 0=None, 1=SSL, 2=STARTTLS
    'connect_timeout' => 10,      // Seconds
    'debug' => true,              // Enable debug logging
    'disable_emails' => false     // Disable all emails
]);

// Get raw API response
$response = VirtualizorAdmin::config()->getEmailSettings(raw: true);
```

Email Settings Response:
```php
[
    'use_php_mail' => false,
    'server' => 'smtp.example.com',
    'port' => 587,
    'username' => 'user@example.com',
    'password' => 'password',
    'smtp_security' => 2,
    'connect_timeout' => 10,
    'debug' => true,
    'emails_disabled' => false,
    'timestamp' => 1536246723,
    'time_taken' => '0.238'
]
```

// Get server information
$info = VirtualizorAdmin::config()->getServerInfo();

// Get raw API response
$response = VirtualizorAdmin::config()->getServerInfo(raw: true);
```

Server Info Response:
```php
[
    'path' => '/usr/local/virtualizor',
    'api' => [
        'key' => 'your_api_key',
        'pass' => 'your_api_pass'
    ],
    'kernel' => 'kvm',
    'vps_count' => 1,
    'version' => [
        'number' => '2.9.7',
        'patch' => 0
    ],
    'timestamp' => 1536246723,
    'time_taken' => '0.238'
]
```

// Get license information
$license = VirtualizorAdmin::config()->getLicenseInfo();

// Update license key
$success = VirtualizorAdmin::config()->updateLicense(
    'VIRTD-12345-67890-12345-67890',  // License key
    true                              // Optional: refresh after update
);

// Get raw API response
$response = VirtualizorAdmin::config()->getLicenseInfo(raw: true);
```

License Info Response:
```php
[
    'license' => [
        'key' => 'VIRTD-81008-78272-55853-14018',
        'type' => [
            'id' => -1,
            'name' => 'Trial'
        ],
        'status' => [
            'active' => true,
            'text' => 'Active'
        ],
        'vps_limit' => 0,
        'expiry' => [
            'date' => '20181231',
            'text' => '31/12/2018 GMT'
        ]
    ],
    'timestamp' => 1540373645,
    'time_taken' => '1.230'
]
```

// Change server hostname
$success = VirtualizorAdmin::config()->changeHostname('server.example.com');

// Get raw API response
$response = VirtualizorAdmin::config()->changeHostname('server.example.com', raw: true);
```

Change Hostname Response:
```php
[
    'title' => 'Hostname',
    'done' => 1,
    'timestamp' => 1536247931,
    'time_taken' => '0.246'
]
```

Note: The hostname change will only take effect after the server is rebooted.

// Enable maintenance mode with custom message
$success = VirtualizorAdmin::config()->enableMaintenanceMode([
    'subject' => 'System Maintenance',
    'message' => 'The system is currently undergoing maintenance.'
]);

// Enable maintenance mode with default message
$success = VirtualizorAdmin::config()->enableMaintenanceMode();

// Disable maintenance mode
$success = VirtualizorAdmin::config()->disableMaintenanceMode();

// Get raw API response
$response = VirtualizorAdmin::config()->enableMaintenanceMode($options, raw: true);
```

Maintenance Mode Response:
```php
[
    'title' => 'Maintenance',
    'done' => 1,
    'timestamp' => 1471392834,
    'time_taken' => '0.156'
]
```

Note: When maintenance mode is enabled, end users will not be able to access their panels.

// Get kernel configuration
$config = VirtualizorAdmin::config()->getKernelConfig();

// Get raw API response
$response = VirtualizorAdmin::config()->getKernelConfig(raw: true);
```

Kernel Configuration Response:
```php
[
    'config' => [
        'global' => [
            'VIRTUOZZO' => 'yes',
            'LOCKDIR' => '/vz/lock',
            'DUMPDIR' => '/vz/dump',
            'VE0CPUUNITS' => '1000'
        ],
        'logging_parameters' => [
            'LOGGING' => 'yes',
            'LOGFILE' => '/var/log/vzctl.log',
            'LOG_LEVEL' => '0',
            'VERBOSE' => '0'
        ],
        'disk_quota_parameters' => [
            'DISK_QUOTA' => 'yes',
            'VZFASTBOOT' => 'no'
        ],
        // ... more sections
    ],
    'raw_config' => '## Global parameters\nVIRTUOZZO=yes\n...',
    'timestamp' => 1473905520,
    'time_taken' => '0.126'
]
```

The configuration is parsed into a structured array for easier access, while preserving the raw configuration string.

// Get default VPS configuration
$config = VirtualizorAdmin::config()->getDefaultVpsConfig();

// Update default VPS configuration
$success = VirtualizorAdmin::config()->updateDefaultVpsConfig('
    ONBOOT="yes"
    KMEMSIZE="unlimited"
    LOCKEDPAGES="unlimited"
    PRIVVMPAGES="unlimited"
    SHMPAGES="unlimited"
    NUMPROC="unlimited"
    PHYSPAGES="0:262144"
    SWAPPAGES="0:262144"
    # ... more configuration
');

// Get raw API response
$response = VirtualizorAdmin::config()->getDefaultVpsConfig(raw: true);
```

Default VPS Configuration Response:
```php
[
    'config' => [
        'ONBOOT' => 'yes',
        'KMEMSIZE' => 'unlimited',
        'LOCKEDPAGES' => 'unlimited',
        'PRIVVMPAGES' => 'unlimited',
        'SHMPAGES' => 'unlimited',
        'NUMPROC' => 'unlimited',
        'PHYSPAGES' => '0:262144',
        'SWAPPAGES' => '0:262144',
        // ... more settings
    ],
    'raw_config' => '###################################################\n...',
    'timestamp' => 1473915377,
    'time_taken' => '0.097'
]
```

Note: This configuration is only applicable for OpenVZ VPS containers.

// Check for updates
$updates = VirtualizorAdmin::config()->checkUpdates();

// Apply available updates
$success = VirtualizorAdmin::config()->applyUpdates();

// Get raw API response
$response = VirtualizorAdmin::config()->checkUpdates(raw: true);
```

Updates Response:
```php
[
    'updated' => true,
    'version' => [
        'number' => '2.9.8',
        'patch' => 1
    ],
    'update' => [
        'message' => 'The Virtualizor Team has released Virtualizor 2.9.8.0...',
        'mode' => 1,
        'link' => 'https://s3.softaculous.com/a/virtualizor/updates.php?give=2.9.8.1',
        'redirect' => '/index.php'
    ],
    'timestamp' => 1540377807,
    'time_taken' => '1.249'
]
```

The update process runs in the background. You can monitor its progress through the task system.

// Get email templates
$templates = VirtualizorAdmin::config()->getEmailTemplates();

// Get raw API response
$response = VirtualizorAdmin::config()->getEmailTemplates(raw: true);
```

Email Templates Response:
```php
[
    'templates' => [
        'general' => [
            'addvs' => [
                'name' => 'addvs',
                'subject' => 'mail_addvs_sub',
                'body' => 'mail_addvs'
            ],
            'rebuildvs' => [
                'name' => 'rebuildvs',
                'subject' => 'mail_rebuildvs_sub',
                'body' => 'mail_rebuildvs'
            ]
        ],
        'admin' => [
            'admin_addvs' => [
                'name' => 'admin_addvs',
                'subject' => 'admin_mail_addvs_sub',
                'body' => 'admin_mail_addvs'
            ]
        ],
        'billing' => [
            'billing_warn_users' => [
                'name' => 'billing_warn_users',
                'subject' => 'billing_warn_users_sub',
                'body' => 'billing_warn_users_body'
            ]
        ],
        'suspension' => [
            'suspend_vps' => [
                'name' => 'suspend_vps',
                'subject' => 'suspend_vps_sub',
                'body' => 'suspend_vps_body'
            ]
        ]
    ],
    'timestamp' => 1536251967,
    'time_taken' => '0.208'
]
```

Templates are organized by category for easier management: general, admin, billing, suspension, and webuzo.

// Update email template
$success = VirtualizorAdmin::config()->updateEmailTemplate(
    'addvs',                        // Template name
    'New VPS Created',              // Email subject
    'Your VPS has been created...'  // Email content
);

// Get raw API response
$response = VirtualizorAdmin::config()->updateEmailTemplate(
    'addvs',
    'New VPS Created',
    'Your VPS has been created...',
    raw: true
);
```

Email Template Update Response:
```php
[
    'title' => 'Edit Email Template',
    'done' => 1,
    'emailtemp' => null,
    'timestamp' => 1480485898,
    'time_taken' => '0.104'
]
```

Available template names can be retrieved using the getEmailTemplates() method. The templates are used for various system notifications like VPS creation, deletion, suspension, etc.

// Reset email template to default
$success = VirtualizorAdmin::config()->resetEmailTemplate('addvs');

// Get raw API response
$response = VirtualizorAdmin::config()->resetEmailTemplate('addvs', raw: true);
```

Email Template Reset Response:
```php
[
    'title' => 'Edit Email Template',
    'emailtemp' => null,
    'timestamp' => 1536253321,
    'time_taken' => '0.239'
]
```

This will restore the specified email template to its original default content. Available template names can be retrieved using the getEmailTemplates() method.

#### Recipe Management

```php
use CODEIQ\Virtualizor\VirtualizorAdmin;

// List all recipes
$recipes = VirtualizorAdmin::recipes()->list();

// List with filters
$recipes = VirtualizorAdmin::recipes()->list([
    'recipe_id' => 1,              // Filter by recipe ID
    'name' => 'test_recipe*',      // Filter by name (supports wildcards)
    'page' => 1,                   // Page number
    'per_page' => 10               // Results per page
]);

// Get raw API response
$response = VirtualizorAdmin::recipes()->list($filters, raw: true);
```

Recipe List Response:
```php
[
    'recipes' => [
        [
            'id' => 1,
            'name' => 'hello',
            'code' => '#!/bin/sh\ndate >> /root/date.txt;\n',
            'description' => '',
            'logo' => '',
            'is_active' => true
        ],
        [
            'id' => 2,
            'name' => 'api recipe',
            'code' => '#!/bin/sh\ndate > /tmp/recipe.txt',
            'description' => 'This is the description for test recipe',
            'logo' => '',
            'is_active' => true
        ]
    ],
    'timestamp' => 1536159274,
    'time_taken' => '0.206'
]
```

Recipes are predefined bash scripts that can be executed during VPS creation or on running VPSes (requires restart).

// Update recipe
$success = VirtualizorAdmin::recipes()->update(23, [
    'name' => 'test_recipe1',
    'script' => 'This is your test recipe',
    'description' => 'This is the description for test recipe',
    'logo' => 'https://example.com/logo.png',  // Optional
    'shell' => 'bash'                          // Optional: sh, bash, ksh, zsh
]);

// Get raw API response
$response = VirtualizorAdmin::recipes()->update(23, $params, raw: true);
```

Recipe Update Response:
```php
[
    'title' => 'Edit Recipe',
    'done' => 1,
    'done_msg' => 'Recipe has been updated',
    'timestamp' => 1536159013,
    'time_taken' => '0.204'
]
```

Only provide the parameters you want to update. Omitted parameters will keep their current values.

// Delete single recipe
$success = VirtualizorAdmin::recipes()->delete(19);

// Delete multiple recipes
$success = VirtualizorAdmin::recipes()->delete([17, 18, 19]);

// Get raw API response
$response = VirtualizorAdmin::recipes()->delete($recipeIds, raw: true);
```

Recipe Delete Response:
```php
[
    'title' => 'Recipes',
    'done' => ['19'],
    'recipe' => [
        // Remaining recipes
    ],
    'timestamp' => 1477375332,
    'time_taken' => '0.076'
]
```

// Activate single recipe
$success = VirtualizorAdmin::recipes()->activate(7);

// Activate multiple recipes
$success = VirtualizorAdmin::recipes()->activate([7, 8, 9]);

// Get raw API response
$response = VirtualizorAdmin::recipes()->activate($recipeIds, raw: true);
```

Recipe Activate Response:
```php
[
    'title' => 'Recipes',
    'done' => [7, 8, 9],
    'recipe' => [
        // Updated recipes
    ],
    'timestamp' => 1536162440,
    'time_taken' => '0.223'
]
```

Only activated recipes are visible to end users and can be executed during VPS creation or on running VPSes.

// Deactivate single recipe
$success = VirtualizorAdmin::recipes()->deactivate(7);

// Deactivate multiple recipes
$success = VirtualizorAdmin::recipes()->deactivate([7, 8, 9]);

// Get raw API response
$response = VirtualizorAdmin::recipes()->deactivate($recipeIds, raw: true);
```

Recipe Deactivate Response:
```php
[
    'title' => 'Recipes',
    'done' => [7, 8, 9],
    'recipe' => [
        // Updated recipes
    ],
    'timestamp' => 1536162598,
    'time_taken' => '0.256'
]
```

Deactivated recipes will not be visible to end users and cannot be executed during VPS creation or on running VPSes.
