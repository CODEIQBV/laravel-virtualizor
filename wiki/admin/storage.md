## Storage Management

### List Storage

```php
use CODEIQ\Virtualizor\VirtualizorAdmin;

// List all storage
$storage = VirtualizorAdmin::storage()->list();

// List with filters
$storage = VirtualizorAdmin::storage()->list([
    'name' => 'Default Storage',  // Filter by storage name
    'path' => '/dev/VolGroup'     // Filter by storage path
]);

// List with pagination
$storage = VirtualizorAdmin::storage()->list([], 1, 10);

// Get raw API response
$response = VirtualizorAdmin::storage()->list([], 1, 50, raw: true);

// Edit storage configuration
$success = VirtualizorAdmin::storage()->edit(113, [
    'name' => 'QCOW File Storage',           // required
    'oversell' => 150,                       // optional: oversell percentage
    'alert_threshold' => 90,                 // optional: alert threshold percentage
    'primary_storage' => true                // optional: set as primary storage
]);

// Get raw API response
$response = VirtualizorAdmin::storage()->edit(113, [
    'name' => 'QCOW File Storage'
], raw: true);

// Delete a single storage
$success = VirtualizorAdmin::storage()->delete(2);

// Delete multiple storages
$success = VirtualizorAdmin::storage()->delete([3, 4]);

// Get raw API response
$response = VirtualizorAdmin::storage()->delete(2, raw: true);

// Create new storage
$storageId = VirtualizorAdmin::storage()->create([
    'name' => 'Test Storage',           // required
    'path' => '/dev/vg/thin_pool',      // required
    'type' => 'thin block',             // required
    'serid' => 0,                       // required: server ID or array of IDs for shared storage
    'format' => 'raw',                  // required: raw, vhd, qcow2
    'primary_storage' => true,          // optional: set as primary storage
    'oversell' => 150,                  // optional: overcommit space in GB
    'alert_threshold' => 90,            // optional: alert threshold percentage
    'lightbit_project' => 'project1'    // optional: lightbits project name
]);

// Get raw API response
$response = VirtualizorAdmin::storage()->create([
    'name' => 'Test Storage',
    'path' => '/dev/vg/thin_pool',
    'type' => 'thin block',
    'serid' => 0,
    'format' => 'raw'
], raw: true);

// List orphaned disks
$disks = VirtualizorAdmin::storage()->listOrphanedDisks();

// List with filters
$disks = VirtualizorAdmin::storage()->listOrphanedDisks([
    'st_id' => 3,                    // Filter by storage ID
    'st_type' => 'block',           // Filter by storage type
    'disk_path' => '/dev/vg/orphan' // Filter by disk path
]);

// Get raw API response
$response = VirtualizorAdmin::storage()->listOrphanedDisks([], raw: true);
```

Storage List Response:
```php
[
    'storages' => [
        [
            'id' => 773,
            'uuid' => 'nonm2bfqpnbhehyb',
            'name' => 'Default Storage',
            'path' => '/dev/VolGroup',
            'type' => 'block',
            'format' => '',
            'size' => [
                'total' => 1862.52,
                'free' => 1332.66,
                'used' => 529.86
            ],
            'settings' => [
                'oversell' => 0,
                'alert_threshold' => 0.00,
                'is_primary' => true,
                'last_alert' => 0
            ]
        ]
    ],
    'server_mappings' => [
        [
            'stid' => '773',
            'serid' => '0',
            'sgid' => '-2'
        ]
    ],
    'timestamp' => 1535546599
]
```

Raw API Response:
```php
{
    'title' => 'Storage Overview',
    'storage' => [
        '773' => [
            'stid' => '773',
            'st_uuid' => 'nonm2bfqpnbhehyb',
            'name' => 'Default Storage',
            'path' => '/dev/VolGroup',
            'type' => 'block',
            'format' => '',
            'size' => '1862.52',
            'free' => '1332.66',
            'oversell' => '0',
            'alert_threshold' => '0.00',
            'primary_storage' => '1',
            'last_alert' => '0'
        ]
    ],
    'storage_servers' => [
        [
            'stid' => '773',
            'serid' => '0',
            'sgid' => '-2'
        ]
    ],
    'timenow' => 1535546599,
    'time_taken' => '0.076'
}
```
