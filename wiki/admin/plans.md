#### Plan Management

```php
use CODEIQ\Virtualizor\VirtualizorAdmin;

// List all plans
$plans = VirtualizorAdmin::plans()->list();

// List with filters
$plans = VirtualizorAdmin::plans()->list([
    'planname' => 'Dummy',  // Filter by plan name
    'ptype' => 'kvm'       // Filter by virtualization type
]);

// List with pagination
$plans = VirtualizorAdmin::plans()->list([], 1, 20);

// Get raw API response
$response = VirtualizorAdmin::plans()->list(raw: true);

// Create a new plan
$planId = VirtualizorAdmin::plans()->create([
    'plan_name' => 'test_api_plan',
    'cplan' => 'kvm',                // Virtualization type
    'disk_space' => 10,              // GB
    'guaranteed_ram' => 1024,        // MB
    'swapram' => 1024,              // MB
    'bandwidth' => 0,                // 0 for unlimited
    'cpu_units' => 1024,
    'cpu_cores' => 4,
    'percent_cpu' => 0,
    'is_enabled' => true,
    // Optional parameters
    'network_speed' => 0,            // KB/s, 0 for unlimited
    'upload_speed' => 0,             // KB/s, 0 for unlimited
    'ips' => 1,                      // Number of IPv4 addresses
    'ips6' => 0,                     // Number of IPv6 addresses
    'ips6_subnet' => 0,              // Number of IPv6 subnets
    'control_panel' => 'cpanel',     // Control panel to install
    'vnc_keymap' => 'en-us'         // VNC keyboard layout
]);

// Edit a plan
$success = VirtualizorAdmin::plans()->edit(4, [
    'plan_name' => 'test_api_edit',
    'cplan' => 'kvm',
    'disk_space' => 10,
    'guaranteed_ram' => 1024,
    'swapram' => 1024,
    'bandwidth' => 0,
    'cpu_units' => 1024,
    'cpu_cores' => 4,
    'percent_cpu' => 0,
    'is_enabled' => true
]);

// Delete a single plan
$result = VirtualizorAdmin::plans()->delete(2);

// Delete multiple plans
$result = VirtualizorAdmin::plans()->delete([3, 4]);

// Get raw API response
$response = VirtualizorAdmin::plans()->delete(2, raw: true);

// List user plans
$userPlans = VirtualizorAdmin::plans()->listUserPlans();

// List user plans with filters
$userPlans = VirtualizorAdmin::plans()->listUserPlans([
    'planname' => 'Basic Plan',
    'ptype' => 'kvm'
]);

// Get raw API response
$response = VirtualizorAdmin::plans()->listUserPlans(raw: true);

// Create a user plan
$planId = VirtualizorAdmin::plans()->createUserPlan([
    'plan_name' => 'api_plan',          // required
    'priority' => 0,                    // required: 0=normal, 1=admin, 2=cloud
    'dnsplan_id' => 1,                  // optional: DNS plan ID
    'acl_id' => 2,                      // optional: ACL ID for admin users
    'num_vs' => 5,                      // optional: max VPS count (0=unlimited)
    'inhouse_billing' => true,          // optional: enable billing
    'num_users' => 10,                  // optional: max sub-users (0=unlimited)
    'space' => 100,                     // optional: max disk space in GB
    'ram' => 4096,                      // optional: max RAM in MB
    'burst' => 8192,                    // optional: max burst RAM in MB
    'bandwidth' => 1000,                // optional: max bandwidth in GB
    'cpu' => 1000,                      // optional: CPU weight
    'cores' => 4,                       // optional: max cores per VPS
    'cpu_percent' => 100,               // optional: CPU % per core
    'allowed_virts' => ['kvm', 'xen'],  // optional: allowed virtualization types
    'band_suspend' => true,             // optional: suspend on bandwidth overuse
    'service_period' => 1               // optional: billing cycle start day (1-31)
]);

// Get raw API response
$response = VirtualizorAdmin::plans()->createUserPlan($params, raw: true);

// Edit a user plan
$success = VirtualizorAdmin::plans()->editUserPlan(5, [
    'plan_name' => 'api_plan',          // required
    'priority' => 0,                    // required: 0=normal, 1=admin, 2=cloud
    'dnsplan_id' => 1,                  // optional: DNS plan ID
    'acl_id' => 2,                      // optional: ACL ID for admin users
    'num_vs' => 5,                      // optional: max VPS count (0=unlimited)
    'inhouse_billing' => true,          // optional: enable billing
    'num_users' => 10,                  // optional: max sub-users (0=unlimited)
    'space' => 100,                     // optional: max disk space in GB
    'ram' => 4096,                      // optional: max RAM in MB
    'burst' => 8192,                    // optional: max burst RAM in MB
    'bandwidth' => 1000,                // optional: max bandwidth in GB
    'cpu' => 1000,                      // optional: CPU weight
    'cores' => 4,                       // optional: max cores per VPS
    'cpu_percent' => 100,               // optional: CPU % per core
    'allowed_virts' => ['kvm', 'xen'],  // optional: allowed virtualization types
    'band_suspend' => true,             // optional: suspend on bandwidth overuse
    'service_period' => 1               // optional: billing cycle start day (1-31)
]);

// Get raw API response
$response = VirtualizorAdmin::plans()->editUserPlan(5, $params, raw: true);
```

Plan List Response:
```php
[
    'plans' => [
        [
            'id' => 1,
            'name' => 'Dummy',
            'virtualization' => 'kvm',
            'resources' => [
                'ips' => [
                    'ipv4' => 1,
                    'ipv6' => 0,
                    'ipv6_subnets' => 0,
                    'internal' => 0
                ],
                'storage' => [
                    'space' => 2.0,
                    'inodes' => 0
                ],
                'memory' => [
                    'ram' => 128,
                    'burst' => 0,
                    'swap' => 128
                ],
                'cpu' => [
                    'cpu_units' => 1000,
                    'cores' => 1,
                    'percent' => 0.00
                ]
            ],
            'settings' => [
                'is_enabled' => true,
                'band_suspend' => false,
                'admin_managed' => false
            ]
        ]
    ],
    'timestamp' => 1535713000,
    'time_taken' => '0.207'
]
```

User Plan List Response:
```php
[
    'plans' => [
        [
            'id' => 1,
            'name' => 'New Plan',
            'type' => 0,
            'acl_id' => 0,
            'billing' => [
                'inhouse' => false,
                'max_cost' => 0
            ],
            'limits' => [
                'vps' => 0,
                'users' => 0,
                'space' => 0,
                'ram' => 0,
                'burst' => 0,
                'bandwidth' => 0,
                'cpu' => [
                    'units' => 0,
                    'cores' => 4,
                    'percent' => 0,
                    'num_cores' => 0
                ]
            ],
            'settings' => [
                'allowed_virts' => [],
                'server_groups' => [],
                'media_groups' => [],
                'dns_plan_id' => 0,
                'service_period' => 1,
                'band_suspend' => false
            ],
            'created_at' => 1534518872
        ]
    ],
    'timestamp' => 1537470364,
    'time_taken' => '0.136'
]
```

#### DNS Plan Management

```php
use CODEIQ\Virtualizor\VirtualizorAdmin;

// List all DNS plans
$plans = VirtualizorAdmin::dns()->list();

// List with filters
$plans = VirtualizorAdmin::dns()->list([
    'planname' => 'test'  // Filter by plan name
]);

// List with pagination
$plans = VirtualizorAdmin::dns()->list([], 1, 10);

// Get raw API response
$response = VirtualizorAdmin::dns()->list(raw: true);

// Create a new DNS plan
$planId = VirtualizorAdmin::dns()->create([
    'plan_name' => 'test',           // required: unique plan name
    'dnsserverid' => 10,            // required: DNS server ID
    'maxdomains' => 1000,           // required: maximum number of domains
    'maxdomainsrec' => 1999,        // required: maximum records per domain
    'ttl' => 10000                  // required: Time To Live value
]);

// Get raw API response
$response = VirtualizorAdmin::dns()->create($params, raw: true);

// Edit a DNS plan
$success = VirtualizorAdmin::dns()->edit(17, [
    'plan_name' => 'test-api-dns',    // required: unique plan name
    'dnsserverid' => 12,              // required: DNS server ID
    'maxdomains' => 1000,             // required: maximum number of domains
    'maxdomainsrec' => 1000,          // required: maximum records per domain
    'ttl' => 990                      // required: Time To Live value
]);

// Get raw API response
$response = VirtualizorAdmin::dns()->edit(17, $params, raw: true);

// Delete a single DNS plan
$result = VirtualizorAdmin::dns()->delete(4);

// Delete multiple DNS plans
$result = VirtualizorAdmin::dns()->delete([4, 5]);

// Get raw API response
$response = VirtualizorAdmin::dns()->delete(4, raw: true);
```

DNS Plan List Response:
```php
[
    'plans' => [
        [
            'id' => 1,
            'pdns_id' => 10,
            'name' => 'test',
            'limits' => [
                'max_domains' => 1000,
                'max_domain_records' => 1999,
                'default_ttl' => 10000
            ],
            'server_name' => null
        ]
    ],
    'timestamp' => 1535717662,
    'time_taken' => '0.255'
]
```

// List backup plans
$plans = VirtualizorAdmin::backup()->listPlans();

// List with filters
$plans = VirtualizorAdmin::backup()->listPlans([
'planname' => 'Daily Backup'  // Filter by plan name
]);

// List with pagination
$plans = VirtualizorAdmin::backup()->listPlans([], 1, 10);

// Get raw API response
$response = VirtualizorAdmin::backup()->listPlans(raw: true);
```

// Delete a single user plan
$result = VirtualizorAdmin::plans()->deleteUserPlan(1);

// Delete multiple user plans
$result = VirtualizorAdmin::plans()->deleteUserPlan([2, 3]);

// Get raw API response
$response = VirtualizorAdmin::plans()->deleteUserPlan(1, raw: true);
```

// Create a backup plan
$planId = VirtualizorAdmin::backup()->createPlan([
'plan_name' => 'api backup',       // required: plan name
'type' => 'LOCAL',                // required: LOCAL, FTP, or SSH
'dir' => '/tmp/api_backup/',      // required: backup directory
'freq' => 'hourly',               // required: hourly, daily, weekly, monthly
'hourly_freq' => 1,               // required: 0-23
'hrs' => 0,                       // required: 0-23 (hour to start)
'min' => 0,                       // required: 0-59 (minute to start)
'day' => 1,                       // required: 1-7 (day of week, 1=Monday)
'date' => 1,                      // required: 1-31 (day of month)
'rotation' => 1,                  // required: 0-10 backups per VPS (0=unlimited)
'backup_limit' => 0,              // required: -1 to 10 manual backups per month
'restore_limit' => 0,             // required: -1 to 10 restores per month
'nice' => 0,                      // required: -20 to 19 CPU priority
'ionice_prio' => 0,              // required: 0-7 IO priority
'ionice_class' => 3,             // required: 1=Real time, 2=Best Effort, 3=Idle
'compression' => true,           // optional: enable compression
'disabled' => false              // optional: disable plan
]);

// Get raw API response
$response = VirtualizorAdmin::backup()->createPlan($params, raw: true);

// Edit a backup plan
$success = VirtualizorAdmin::backup()->editPlan(2, [
'plan_name' => 'api backup',       // required: plan name
'type' => 'LOCAL',                // required: LOCAL, FTP, or SSH
'dir' => '/tmp/api_backup/',      // required: backup directory
'freq' => 'hourly',               // required: hourly, daily, weekly, monthly
'hourly_freq' => 1,               // required: 0-23
'hrs' => 0,                       // required: 0-23 (hour to start)
'min' => 0,                       // required: 0-59 (minute to start)
'day' => 1,                       // required: 1-7 (day of week, 1=Monday)
'date' => 1,                      // required: 1-31 (day of month)
'rotation' => 1,                  // required: 0-10 backups per VPS (0=unlimited)
'backup_limit' => 0,              // required: -1 to 10 manual backups per month
'restore_limit' => 0,             // required: -1 to 10 restores per month
'nice' => 0,                      // required: -20 to 19 CPU priority
'ionice_prio' => 0,              // required: 0-7 IO priority
'ionice_class' => 3,             // required: 1=Real time, 2=Best Effort, 3=Idle
'compression' => true,           // optional: enable compression
'disabled' => false              // optional: disable plan
]);

// Get raw API response
$response = VirtualizorAdmin::backup()->editPlan(2, $params, raw: true);
```

// Delete a single backup plan
$success = VirtualizorAdmin::backup()->deletePlan(1);

// Delete multiple backup plans
$success = VirtualizorAdmin::backup()->deletePlan([1, 2]);

// Get raw API response
$response = VirtualizorAdmin::backup()->deletePlan(1, raw: true);
```
