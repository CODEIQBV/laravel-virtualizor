#### IP Pool Management

```php
use CODEIQ\Virtualizor\VirtualizorAdmin;

// List all IP pools
$pools = VirtualizorAdmin::ipPool()->list();

// List IP pools with pagination
$pools = VirtualizorAdmin::ipPool()->list(1, 10);

// Get raw API response
$response = VirtualizorAdmin::ipPool()->list(raw: true);

// Create IPv4 pool
$pool = VirtualizorAdmin::ipPool()->create([
    'iptype' => 4,                    // required: 4 for IPv4, 6 for IPv6
    'serid' => 0,                     // required: server ID
    'ippool_name' => 'test_api_ipv4', // required
    'gateway' => '192.168.1.47',      // required
    'netmask' => '255.255.255.0',     // required
    'ns1' => '8.8.8.8',              // required
    'ns2' => '8.8.4.4',              // required
    'firstip' => '192.168.1.48',      // required
    'lastip' => '192.168.1.55',       // required
    'routing' => true,                // optional
    'nat' => true                     // optional
]);

// Create IPv6 pool
$pool = VirtualizorAdmin::ipPool()->createIpv6([
    'serid' => 0,                     // required: server ID
    'ippool_name' => 'test_api_ipv6', // required
    'gateway' => '1234:0f:0f:1',      // required
    'netmask' => '48',                // required
    'ns1' => '2001:4860:4860::8888',  // required
    'ns2' => '2001:4860:4860::8844',  // required
    'ipv6_1' => '1234',               // required: first IPv6 segment
    'ipv6_2' => '0f0f',               // required: second IPv6 segment
    'ipv6_3' => '0f0f',               // required: third IPv6 segment
    'ipv6_4' => '0123',               // required: fourth IPv6 segment
    'ipv6_5' => '1000',               // required: fifth IPv6 segment
    'ipv6_6' => '0000',               // required: sixth IPv6 segment
    'ipv6_num' => 50,                 // required: number of IPv6 addresses
    'routing' => true,                // optional
    'vlan' => false,                  // optional
    'vlan_bridge' => 'br0',           // optional
    'mtu' => 1500                     // optional
]);

// Get raw API response
$response = VirtualizorAdmin::ipPool()->createIpv6([...], raw: true);

// Create internal IPv4 pool
$pool = VirtualizorAdmin::ipPool()->createInternal([
    'iptype' => 4,                    // required: 4 for IPv4, 6 for IPv6
    'serid' => 0,                     // required: server ID
    'ippool_name' => 'test_api_int',  // required
    'gateway' => '10.10.1.1',         // required
    'netmask' => '255.255.255.0',     // required
    'ns1' => '4.2.2.1',              // required
    'ns2' => '4.2.2.2',              // required
    'firstip' => '10.10.1.3',         // required
    'lastip' => '10.10.1.15',         // required
    'internal_bridge' => 'intbr0',    // required
    'mtu' => 1500                     // optional
]);

// Get raw API response
$response = VirtualizorAdmin::ipPool()->createInternal([...], raw: true);

// Update IPv4 pool
$pool = VirtualizorAdmin::ipPool()->update(546, [
    'ippool_name' => 'dummyips',       // required
    'gateway' => '192.168.1.200',      // required
    'netmask' => '255.255.255.0',      // required
    'ns1' => '8.8.8.8',               // required
    'ns2' => '8.8.4.4',               // required
    'serid' => 0,                      // required
    'nat' => true,                     // optional, IPv4 only
    'routing' => false,                // optional
    'mtu' => 0                         // optional
]);

// Update IPv6 pool
$pool = VirtualizorAdmin::ipPool()->update(546, [
    'ippool_name' => 'dummyips',
    'gateway' => '1234:0f:0f:1',
    'netmask' => '48',
    'ns1' => '2001:4860:4860::8888',
    'ns2' => '2001:4860:4860::8844',
    'serid' => 0,
    'routing' => true,
    'mtu' => 1500
]);

// Get raw API response
$response = VirtualizorAdmin::ipPool()->update(546, [...], raw: true);

// Delete single IP pool
$result = VirtualizorAdmin::ipPool()->delete(7);

// Delete multiple IP pools
$result = VirtualizorAdmin::ipPool()->delete([8, 9, 10]);

// Get raw API response
$response = VirtualizorAdmin::ipPool()->delete(7, raw: true);

// List all IPs
$ips = VirtualizorAdmin::ipPool()->listIps();

// List IPs with pagination
$ips = VirtualizorAdmin::ipPool()->listIps(1, 20);

// Get raw API response
$response = VirtualizorAdmin::ipPool()->listIps(raw: true);

// Search IP pools with filters
$pools = VirtualizorAdmin::ipPool()->search([
    'poolname' => 'Live IPv6',      // optional: pool name filter
    'poolgateway' => '',            // optional: gateway filter
    'netmask' => '',                // optional: netmask filter
    'nameserver' => '',             // optional: nameserver filter
    'servers_search' => -1          // optional: server ID (-1 for all servers)
]);

// Search with pagination
$pools = VirtualizorAdmin::ipPool()->search([
    'poolname' => 'Live IPv6'
], 1, 10);

// Get raw API response
$response = VirtualizorAdmin::ipPool()->search([...], raw: true);

// Search IPs with filters
$ips = VirtualizorAdmin::ipPool()->searchIps([
    'ipsearch' => '192.168.2.110',     // optional: search by IP address
    'ippoolsearch' => '',              // optional: search by IP pool name
    'ippid' => null,                   // optional: search by IP pool ID
    'macsearch' => '',                 // optional: search by MAC address
    'vps_search' => '',                // optional: search by VPS hostname
    'servers_search' => null,          // optional: search by server ID
    'lockedsearch' => 'showlocked'     // optional: filter by lock status (showlocked/hidelocked)
]);

// Search with pagination
$ips = VirtualizorAdmin::ipPool()->searchIps([
    'ipsearch' => '192.168.2.110'
], 1, 10);

// Get raw API response
$response = VirtualizorAdmin::ipPool()->searchIps([...], raw: true);

// Add single IPv4 address
$result = VirtualizorAdmin::ipPool()->addIpv4([
    'ips' => ['192.168.1.200'],     // required: array of IPs
    'macs' => [''],                 // optional: array of MAC addresses
    'ippid' => null,                // optional: IP pool ID
    'ip_serid' => 0                 // optional: server ID
]);

// Add multiple IPv4 addresses
$result = VirtualizorAdmin::ipPool()->addIpv4([
    'ips' => [
        '192.168.1.201',
        '192.168.1.202',
        '192.168.1.203'
    ],
    'macs' => [
        '00:11:22:33:44:55',
        '00:11:22:33:44:56',
        '00:11:22:33:44:57'
    ]
]);

// Add IPv4 range
$result = VirtualizorAdmin::ipPool()->addIpv4([
    'firstip' => '192.168.1.100',   // required for range
    'lastip' => '192.168.1.150',    // required for range
    'ippid' => 1,                   // optional: IP pool ID
    'ip_serid' => 0                 // optional: server ID
]);

// Get raw API response
$response = VirtualizorAdmin::ipPool()->addIpv4([...], raw: true);

// Add single IPv6 address with segments
$result = VirtualizorAdmin::ipPool()->addIpv6([
    'ips6' => [
        ['1234', '0f0f', '0f0f', '0123', '1000', '0000', 'eca7', '071f']
    ],
    'macs' => [''],                 // optional: array of MAC addresses
    'ippid' => 3,                   // optional: IP pool ID
    'ip_serid' => 0                 // optional: server ID
]);

// Add multiple IPv6 addresses
$result = VirtualizorAdmin::ipPool()->addIpv6([
    'ips6' => [
        ['1234', '0f0f', '0f0f', '0123', '1000', '0000', 'eca7', '071f'],
        ['1234', '0f0f', '0f0f', '0123', '1000', '0000', 'eca7', '072f'],
        ['1234', '0f0f', '0f0f', '0123', '1000', '0000', 'eca7', '073f']
    ],
    'macs' => [
        '00:11:22:33:44:55',
        '00:11:22:33:44:56',
        '00:11:22:33:44:57'
    ]
]);

// Generate IPv6 addresses using segments
$result = VirtualizorAdmin::ipPool()->addIpv6([
    'ipv6_1' => '1234',            // first segment
    'ipv6_2' => '0f0f',            // second segment
    'ipv6_3' => '0f0f',            // third segment
    'ipv6_4' => '0123',            // fourth segment
    'ipv6_5' => '1000',            // fifth segment
    'ipv6_6' => '0000',            // sixth segment
    'ipv6_num' => 10,              // number of IPs to generate
    'ippid' => 3,                  // optional: IP pool ID
    'ip_serid' => 0                // optional: server ID
]);

// Get raw API response
$response = VirtualizorAdmin::ipPool()->addIpv6([...], raw: true);

// Edit IP address
$result = VirtualizorAdmin::ipPool()->editIp(536, [
    'ip' => '192.168.1.25',        // required: new IP address
    'mac_addr' => '',              // optional: MAC address (auto-generated if empty)
    'locked' => false,             // optional: lock status
    'note' => ''                   // optional: note about the IP
]);

// Get raw API response
$response = VirtualizorAdmin::ipPool()->editIp(536, [...], raw: true);

// List IPv6 subnets
$subnets = VirtualizorAdmin::ipPool()->listIpv6Subnets();

// List IPv6 subnets with pagination
$subnets = VirtualizorAdmin::ipPool()->listIpv6Subnets(1, 20);

// Get raw API response
$response = VirtualizorAdmin::ipPool()->listIpv6Subnets(raw: true);

// Search IPv6 subnets with filters
$subnets = VirtualizorAdmin::ipPool()->searchIpv6Subnets([
    'ipsearch' => '',                // optional: search by IPv6 subnet
    'ippoolsearch' => 'pool7',       // optional: search by IP pool name
    'ippid' => null,                 // optional: search by IP pool ID
    'lockedsearch' => 'hidelocked'   // optional: filter by lock status (showlocked/hidelocked)
]);

// Search with pagination
$subnets = VirtualizorAdmin::ipPool()->searchIpv6Subnets([
    'ippoolsearch' => 'pool7'
], 1, 10);

// Get raw API response
$response = VirtualizorAdmin::ipPool()->searchIpv6Subnets([...], raw: true);

// Edit IPv6 subnet
$result = VirtualizorAdmin::ipPool()->editIpv6Subnet(104, [
    'ip' => '1234:0f0f:0f0f:0123:1000:0000:ecf7:0001', // required
    'netmask' => 80,                                    // optional: 64, 80, 96, or 112
    'locked' => true                                    // optional: lock status
]);

// Get raw API response
$response = VirtualizorAdmin::ipPool()->editIpv6Subnet(104, [...], raw: true);

// Delete single IPv6 subnet
$result = VirtualizorAdmin::ipPool()->deleteIpv6Subnet(538);

// Delete multiple IPv6 subnets
$result = VirtualizorAdmin::ipPool()->deleteIpv6Subnet([538, 539, 540]);

// Get raw API response
$response = VirtualizorAdmin::ipPool()->deleteIpv6Subnet(538, raw: true);

// Add IPv6 subnet with segments
$result = VirtualizorAdmin::ipPool()->addIpv6Subnet([
    'netmask' => 48,                // required: 48, 64, 80, 96, or 112
    'input_netmask' => 40,          // required: 32-108 in increments of 4
    'ipv6_1' => '102f',             // required
    'ipv6_2' => '4526',             // required
    'ipv6_3' => '1f25',             // required for input_netmask >= 36
    'ipv6_4' => '',                 // required for input_netmask >= 52
    'ipv6_5' => '',                 // required for input_netmask >= 68
    'ipv6_6' => '',                 // required for input_netmask >= 84
    'ipv6_7' => '',                 // required for input_netmask >= 100
    'ipv6_num' => 50,               // required: number of subnets
    'ippid' => 750                  // optional: IP pool ID
]);

// Add specific IPv6 subnets
$result = VirtualizorAdmin::ipPool()->addIpv6Subnet([
    'netmask' => 48,
    'input_netmask' => 40,
    'ips6' => [
        ['1234', '0f0f', '0f0f', '0123', '1000', '0000', 'eca7', '071f']
    ],
    'ipv6_num' => 1,
    'ippid' => 750
]);

// Get raw API response
$response = VirtualizorAdmin::ipPool()->addIpv6Subnet([...], raw: true);
```

IP Pool List Response:
```php
[
    [
        'id' => 3,
        'server_id' => 0,
        'name' => 'Live IPv6',
        'gateway' => '2607:fa98:30:1::1',
        'netmask' => '64',
        'nameservers' => [
            'ns1' => '2001:4860:4860::8888',
            'ns2' => '2001:4860:4860::8844'
        ],
        'is_ipv6' => true,
        'nat' => [
            'enabled' => false,
            'name' => ''
        ],
        'routing' => true,
        'internal' => false,
        'bridge' => '',
        'mtu' => 0,
        'vlan' => 0,
        'ips' => [
            'total' => 50,
            'free' => 45
        ]
    ],
    // ... more IP pools
]
```

Raw API Response:
```php
[
    'title' => 'IP Pool',
    'ippools' => [
        3 => [
            'ippid' => '3',
            'ipp_serid' => '0',
            'ippool_name' => 'Live IPv6',
            'gateway' => '2607:fa98:30:1::1',
            // ... other properties
        ]
    ],
    'timenow' => 1535385866,
    'time_taken' => '0.229'
]
```

Search IP Pools Response:
```php
[
    [
        'id' => 3,
        'server_id' => 0,
        'name' => 'Live IPv6',
        'gateway' => '2607:fa98:30:1::1',
        'netmask' => '64',
        'nameservers' => [
            'ns1' => '2001:4860:4860::8888',
            'ns2' => '2001:4860:4860::8844'
        ],
        'is_ipv6' => true,
        'nat' => [
            'enabled' => false,
            'name' => ''
        ],
        'routing' => true,
        'internal' => false,
        'bridge' => '',
        'mtu' => 0,
        'vlan' => 0
    ]
]
```

Raw API Response:
```php
[
    'title' => 'IP Pool',
    'ippools' => [
        3 => [
            'ippid' => '3',
            'ipp_serid' => '0',
            'ippool_name' => 'Live IPv6',
            'gateway' => '2607:fa98:30:1::1',
            // ... other properties
        ]
    ],
    'timenow' => 1535385866,
    'time_taken' => '0.229'
]
```

Search IPs Response:
```php
[
    [
        'id' => 262,
        'pool_id' => 3,
        'server_id' => 0,
        'vps_id' => 0,
        'address' => '2607:fa98:0030:0001:0000:0002:341e:0945',
        'is_ipv6' => true,
        'netmask' => '',
        'is_primary' => false,
        'mac_address' => '',
        'is_locked' => false,
        'note' => null,
        'pool' => [
            'name' => 'Live IPv6',
            'gateway' => '2607:fa98:30:1::1',
            'netmask' => '64',
            'nameservers' => [
                'ns1' => '2001:4860:4860::8888',
                'ns2' => '2001:4860:4860::8844'
            ],
            'nat' => [
                'enabled' => false,
                'name' => ''
            ],
            'routing' => true,
            'internal' => false,
            'bridge' => '',
            'mtu' => 0,
            'vlan' => 0
        ],
        'hostname' => null
    ],
    // ... more IPs
]
```

Raw API Response:
```php
[
    'title' => 'IP List',
    'ips' => [
        '262' => [
            'ipid' => '262',
            'ippid' => '3',
            'ip_serid' => '0',
            'vpsid' => '0',
            'ip' => '2607:fa98:0030:0001:0000:0002:341e:0945',
            // ... other properties
        ],
        // ... more IPs
    ],
    'timenow' => 1535387001,
    'time_taken' => '0.228'
]
```

Add IPv4 Response:
```php
[
    'success' => true,
    'ips' => [
        '536' => '192.168.1.200'
        // ... more IPs if added
    ]
]
```

Raw API Response:
```php
[
    'title' => 'Add IP',
    'done' => [
        'range' => [
            '536' => '192.168.1.200'
        ],
        'done' => 1
    ],
    'timenow' => 1535388434,
    'time_taken' => '0.217'
]
```

Add IPv6 Response:
```php
[
    'success' => true,
    'ips' => [
        '235615' => '1234:0f0f:0f0f:0123:1000:0000:eca7:071f'
        // ... more IPs if added
    ]
]
```

Raw API Response:
```php
[
    'title' => 'Add IP',
    'done' => [
        'range' => [
            '235615' => '1234:0f0f:0f0f:0123:1000:0000:eca7:071f'
        ],
        'done' => 1
    ],
    'timenow' => 1481873949,
    'time_taken' => '0.104'
]
```

Edit IP Response:
```php
[
    'success' => true,
    'id' => 536,
    'ip' => [
        'ipid' => '536',
        'ippid' => null,
        'ip_serid' => '0',
        'vpsid' => '0',
        'ip' => '192.168.1.25',
        // ... other IP properties
    ],
    'pools' => [
        // IP pool information
    ]
]
```

Raw API Response:
```php
[
    'title' => 'Edit IPs',
    'done' => 1,
    'ippools' => [
        // IP pool information
    ],
    'ip' => [
        'ipid' => '536',
        'ip' => '192.168.1.25',
        // ... other properties
    ],
    'ipid' => 536,
    'timenow' => 1535391685,
    'time_taken' => '0.211'
]
```

Edit IPv6 Subnet Response:
```php
[
    'success' => true,
    'id' => 104,
    'ip' => '1234:0f0f:0f0f:0123:1000:0000:ecf7:0001',
    'netmask' => 80,
    'locked' => true,
    'timestamp' => 1481882455
]
```

Raw API Response:
```php
[
    'title' => 'Edit IPs',
    'timenow' => 1481882455,
    'time_taken' => '0.109'
]
```

Delete IPv6 Subnet Response:
```php
[
    'success' => true,
    'remaining_subnets' => [
        [
            'id' => 104,
            'pool_id' => 6,
            'server_id' => 0,
            'vps_id' => 2206,
            'address' => '1111:1111:1111:0df0:0000:0000:0000:0001',
            'netmask' => '80',
            'is_primary' => false,
            'mac_address' => null,
            'is_locked' => true,
            'pool' => [
                'name' => 'pool7',
                'gateway' => '2a06:8ec0::4',
                'netmask' => '10',
                'nameservers' => [
                    'ns1' => '8.8.8.8',
                    'ns2' => '8.8.4.4'
                ],
                'nat' => [
                    'enabled' => false,
                    'name' => ''
                ],
                'routing' => true,
                'internal' => false,
                'bridge' => '',
                'mtu' => 0,
                'vlan' => 0
            ],
            'hostname' => null
        ],
        // ... more remaining subnets
    ]
]
```

Raw API Response:
```php
[
    'title' => 'IPv6 Subnets',
    'done' => true,
    'ips' => [
        '104' => [
            'ipid' => '104',
            'ippid' => '6',
            'ip_serid' => '0',
            'vpsid' => '2206',
            'ip' => '1111:1111:1111:0df0:0000:0000:0000:0001',
            // ... other properties
        ],
        // ... more subnets
    ],
    'timenow' => 1481883964,
    'time_taken' => '0.147'
]
```
