#### ACL Management

```php
use CODEIQ\Virtualizor\VirtualizorAdmin;

// List all ACLs
$acls = VirtualizorAdmin::acl()->list();

// List ACLs with pagination
$acls = VirtualizorAdmin::acl()->list(page: 1, perPage: 10);

// Get raw API response
$response = VirtualizorAdmin::acl()->list(raw: true);

// Create ACL with specific permissions
$acl = VirtualizorAdmin::acl()->create('Support Staff', [
    'addvs' => true,      // Can add VPS
    'editvs' => true,     // Can edit VPS
    'startvs' => true,    // Can start VPS
    'stopvs' => true,     // Can stop VPS
    'restartvs' => true,  // Can restart VPS
    'users' => true,      // Can view users
    'vs' => true          // Can view VPS list
]);

// Create ACL with raw response
$response = VirtualizorAdmin::acl()->create('Admin Staff', [
    'adduser' => true,    // Can add users
    'edituser' => true,   // Can edit users
    'addvs' => true,      // Can add VPS
    'editvs' => true      // Can edit VPS
], raw: true);

// Update existing ACL
$acl = VirtualizorAdmin::acl()->update(50, 'Support Staff Updated', [
    'addvs' => true,      // Can add VPS
    'editvs' => true,     // Can edit VPS
    'startvs' => true,    // Can start VPS
    'stopvs' => false,    // Cannot stop VPS
    'restartvs' => true,  // Can restart VPS
    'users' => true,      // Can view users
    'vs' => true          // Can view VPS list
]);

// Update ACL with raw response
$response = VirtualizorAdmin::acl()->update(50, 'Admin Staff', [
    'adduser' => true,    // Can add users
    'edituser' => true,   // Can edit users
    'addvs' => false,     // Cannot add VPS
    'editvs' => true      // Can edit VPS
], raw: true);

// Delete single ACL
$result = VirtualizorAdmin::acl()->delete(2);

// Delete multiple ACLs
$result = VirtualizorAdmin::acl()->delete([3, 4]);

// Get raw API response
$response = VirtualizorAdmin::acl()->delete(2, raw: true);
```

ACL Response Format:
```php
[
    'success' => true,
    'name' => 'Support Staff',
    'permissions' => [
        'addvs' => true,
        'editvs' => true,
        // ... other permissions
    ]
]
```

Raw API Response:
```php
[
    'title' => 'Add Administrator ACL',
    'done' => true,
    'timenow' => 1535997030,
    'time_taken' => '0.132'
]
```

ACL List Response Format:
```php
[
    1 => [
        'aclid' => '1',
        'acl_name' => 'Support Staff',
        'act_cluster_statistics' => '1',
        'act_vs' => '1',
        'act_editvs' => '1',
        'act_startvs' => '1',
        // ... other permissions
    ],
    2 => [
        // another ACL
    ]
]
```

Raw API Response:
```php
[
    'title' => 'Administrator Access Control List',
    'acls' => [
        // ACL data as shown above
    ],
    'timenow' => 1536007413,
    'time_taken' => '0.225'
]
```

Available ACL Permissions:
- `add_admin_acl`: Add Admin ACL
- `addbackupserver`: Add Backup Server
- `addbackup_plan`: Add Backup Plan
- `adddnsplan`: Add DNS Plan
- `addippool`: Add IP Pool
- `addips`: Add IPs
- `addiso`: Add ISOs
- `addmg`: Add Media Groups
- `addpdns`: Add PDNS
- `addplan`: Add Plans
- `addrecipe`: Add Recipe
- `addserver`: Add Slave Server
- `addsg`: Add Server Groups
- `addtemplate`: Add Templates
- `adduser`: Add Users
- `adminacl`: View Admin ACLs
- `addvs`: Add VPS
- And many more...

#### Response Formats

List Users Response:
```php
[
    1 => [
        'uid' => '1',
        'email' => 'user@example.com',
        'type' => '0',
        'num_vs' => '5',
        'ram' => '2048',
        'space' => '50',
        'bandwidth' => '1000',
        // ... other user properties
    ],
    2 => [
        // another user
    ]
]
```

Create User Response:
```php
[
    'id' => 60,
    'email' => 'user@example.com',
    'type' => 'user'
]
```

Delete User Response:
```php
[
    123 => [
        'uid' => '123',
        'email' => 'user@example.com',
        'dnsplid' => '0'
    ]
]
```

Suspend/Unsuspend User Response:
```php
[
    'success' => true,
    'users' => [
        123 => [
            'uid' => '123',
            'email' => 'user@example.com',
            'suspended' => '0', // '1' for suspended, '0' or null for active
            // ... other user properties
        ]
    ]
]
```

Raw API Response Example:
```php
[
    'title' => 'Users',
    'users' => [
        // user data
    ],
    'timenow' => 1234567890,
    'time_taken' => '0.123'
]
```
