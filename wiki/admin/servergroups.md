#### Server Group Management

```php
use CODEIQ\Virtualizor\VirtualizorAdmin;

// List all server groups
$groups = VirtualizorAdmin::serverGroups()->list();

// List server groups with name filter
$groups = VirtualizorAdmin::serverGroups()->list([
    'sg_name' => 'test_sg'
]);

// Get raw API response
$response = VirtualizorAdmin::serverGroups()->list(raw: true);

// Create new server group
$group = VirtualizorAdmin::serverGroups()->create([
    'sg_name' => 'US-East',           // required
    'sg_reseller_name' => 'USA',      // optional
    'sg_desc' => 'US East Region',    // optional
    'sg_select' => 0                  // optional: 0 = Least Utilized, 1 = First Available
]);

// Get raw API response
$response = VirtualizorAdmin::serverGroups()->create([...], raw: true);

// Update server group
$group = VirtualizorAdmin::serverGroups()->update(1, [
    'sg_name' => 'US-West',           // required
    'sg_reseller_name' => 'USA',      // optional
    'sg_desc' => 'US West Region',    // optional
    'sg_select' => 1                  // optional: 0 = Least Utilized, 1 = First Available
]);

// Get raw API response
$response = VirtualizorAdmin::serverGroups()->update(1, [...], raw: true);

// Delete single server group
$result = VirtualizorAdmin::serverGroups()->delete(1);

// Delete multiple server groups
$result = VirtualizorAdmin::serverGroups()->delete([1, 2]);

// Get raw API response
$response = VirtualizorAdmin::serverGroups()->delete(1, raw: true);
```

Server Group List Response:
```php
[
    [
        'id' => 0,
        'name' => 'Default',
        'reseller_name' => 'Default',
        'description' => 'Default Group',
        'ha_enabled' => false,
        'total_servers' => 1,
        'servers' => [
            'localhost'
        ]
    ],
    [
        'id' => 2,
        'name' => 'test_sg',
        'reseller_name' => 'US',
        'description' => 'Test Group',
        'ha_enabled' => true,
        'total_servers' => 1,
        'servers' => [
            'test'
        ]
    ]
]
```

Raw API Response:
```php
{
    'title' => 'Server Groups / Regions',
    'servergroups' => [
        '0' => [
            'sgid' => 0,
            'sg_name' => 'Default',
            'sg_reseller_name' => 'Default',
            'sg_desc' => 'Default Group',
            'sg_select' => 0,
            'totalservers' => 1,
            'servers' => [
                'localhost'
            ]
        ],
        // ... more groups
    ],
    'timenow' => 1535543450,
    'time_taken' => '0.225'
}
```

Create Server Group Response:
```php
[
    'success' => true,
    'id' => 4,
    'name' => 'US-East',
    'reseller_name' => 'USA',
    'description' => 'US East Region',
    'selection_mode' => 0
]
```

Raw API Response:
```php
[
    'title' => 'Add Server Group / Regions',
    'done' => 4,  // Server group ID
    'timenow' => 1535486540,
    'time_taken' => '0.214'
]
```

Update Server Group Response:
```php
[
    'success' => true,
    'id' => 1,
    'group' => [
        'sgid' => '1',
        'sg_name' => 'US-West',
        'sg_reseller_name' => 'USA',
        'sg_desc' => 'US West Region',
        'sg_select' => '1'
    ]
]
```

Raw API Response:
```php
[
    'title' => 'Edit Server Group / Region',
    'done' => 1,
    'servergroup' => [
        'sgid' => '1',
        'sg_name' => 'US-West',
        'sg_reseller_name' => 'USA',
        'sg_desc' => 'US West Region',
        'sg_select' => '1'
    ],
    'timenow' => 1535487536,
    'time_taken' => '0.220'
]
```

Delete Server Group Response:
```php
[
    'success' => true,
    'deleted' => [
        1 => [
            'sgid' => '1',
            'sg_name' => 'test@te',
            'sg_desc' => ''
        ]
    ],
    'remaining_groups' => [
        [
            'sgid' => 0,
            'sg_name' => 'Default',
            'sg_reseller_name' => 'Default',
            'sg_desc' => 'Default Group',
            'sg_select' => 0
        ],
        // ... other remaining groups
    ]
]
```

Raw API Response:
```php
[
    'title' => 'Server Groups / Regions',
    'done' => [
        1 => [
            'sgid' => '1',
            'sg_name' => 'test@te',
            'sg_desc' => ''
        ]
    ],
    'servergroups' => [
        // remaining server groups
    ],
    'timenow' => 1535488141,
    'time_taken' => '0.218'
]
```
