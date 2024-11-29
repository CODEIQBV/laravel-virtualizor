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

#### User Management

```php
use CODEIQ\Virtualizor\VirtualizorAdmin;

// List all users
$users = VirtualizorAdmin::users()->list();

// List users with filters
$users = VirtualizorAdmin::users()->list([
    'email' => 'user@example.com',
    'type' => 0 // 0: User, 1: Admin, 2: Cloud
]);

// List users with pagination
$users = VirtualizorAdmin::users()->list([], 1, 50); // page 1, 50 per page

// Get raw API response
$response = VirtualizorAdmin::users()->list([], 1, 50, raw: true);

// Get specific user
try {
    $user = VirtualizorAdmin::users()->get(123);
} catch (\Exception $e) {
    // Handle user not found
}

// Create user
$user = VirtualizorAdmin::users()->create([
    'priority' => 0, // 0: User, 1: Admin, 2: Cloud
    'email' => 'user@example.com',
    'password' => 'secret',
    'fname' => 'John', // optional
    'lname' => 'Doe',  // optional
    'num_vs' => 5,     // max number of VPS
    'ram' => 2048,     // max RAM in MB
    'space' => 50,     // max disk space in GB
    'bandwidth' => 1000, // max bandwidth
    'allowed_virts' => ['kvm', 'proxk'], // optional
    'sgs' => [1, 2],   // server groups, optional
    'mgs' => [1]       // media groups, optional
]);

// Update user
$user = VirtualizorAdmin::users()->update(123, [
    'email' => 'new@example.com',
    'ram' => 4096,
    'cores' => 4,
    'allowed_virts' => ['kvm', 'openvz']
]);

// Delete single user
$result = VirtualizorAdmin::users()->delete(123);

// Delete multiple users
$result = VirtualizorAdmin::users()->delete([123, 124, 125]);

// Delete user and their VMs
$result = VirtualizorAdmin::users()->delete(123, deleteVms: true);

// Get raw response for any method
$response = VirtualizorAdmin::users()->delete(123, raw: true);

// Suspend single user
$result = VirtualizorAdmin::users()->suspend(123);

// Suspend multiple users
$result = VirtualizorAdmin::users()->suspend([123, 124, 125]);

// Unsuspend single user
$result = VirtualizorAdmin::users()->unsuspend(123);

// Unsuspend multiple users
$result = VirtualizorAdmin::users()->unsuspend([123, 124, 125]);

// Get raw API response
$response = VirtualizorAdmin::users()->unsuspend(123, raw: true);

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

#### Backup Server Management

```php
use CODEIQ\Virtualizor\VirtualizorAdmin;

// List all backup servers
$servers = VirtualizorAdmin::backup()->list();

// List backup servers with filters
$servers = VirtualizorAdmin::backup()->list([
    'name' => 'backup1',
    'hostname' => '192.168.1.100',
    'type' => 'SSH'  // SSH or FTP
]);

// List backup servers with pagination
$servers = VirtualizorAdmin::backup()->list([], 1, 10);

// Get raw API response
$response = VirtualizorAdmin::backup()->list(raw: true);

// Create new backup server with password authentication
$server = VirtualizorAdmin::backup()->create([
    'hostname' => '192.168.1.100',  // required
    'name' => 'backup-server-1',    // required
    'type' => 'SSH',                // required: SSH or FTP
    'username' => 'root',           // required
    'password' => 'secret',         // optional if using SSH keys
    'port' => 22                    // required
]);

// Create backup server with SSH key authentication
$server = VirtualizorAdmin::backup()->create([
    'hostname' => '192.168.1.100',
    'name' => 'backup-server-2',
    'type' => 'SSH',
    'username' => 'root',
    'port' => 22,
    'ssh_key' => true,
    'sshpub_key' => 'ssh-rsa AAAAB3NzaC1...',
    'sshpri_key' => '-----BEGIN RSA PRIVATE KEY-----...'
]);

// Create backup server with auto-generated SSH keys
$server = VirtualizorAdmin::backup()->create([
    'hostname' => '192.168.1.100',
    'name' => 'backup-server-3',
    'type' => 'SSH',
    'username' => 'root',
    'port' => 22,
    'ssh_key' => true,
    'gen_key' => true
]);

// Create FTP backup server with FTPS support
$server = VirtualizorAdmin::backup()->create([
    'hostname' => 'ftp.example.com',
    'name' => 'backup-server-4',
    'type' => 'FTP',
    'username' => 'ftpuser',
    'password' => 'ftppass',
    'port' => 21,
    'ftps' => true
]);

// Get raw API response
$response = VirtualizorAdmin::backup()->create([...], raw: true);

// Update backup server with password authentication
$server = VirtualizorAdmin::backup()->update(4, [
    'hostname' => 'lxc.nuftp.com',  // required
    'name' => 'lxc server',         // required
    'type' => 'SSH',                // required: SSH or FTP
    'username' => 'root',           // required
    'password' => 'newpass',        // optional if using SSH keys
    'port' => 2044                  // required
]);

// Update backup server with SSH key authentication
$server = VirtualizorAdmin::backup()->update(4, [
    'hostname' => 'lxc.nuftp.com',
    'name' => 'lxc server',
    'type' => 'SSH',
    'username' => 'root',
    'port' => 2044,
    'ssh_key' => true,
    'sshpub_key' => 'ssh-rsa AAAAB3NzaC1...',
    'sshpri_key' => '-----BEGIN RSA PRIVATE KEY-----...'
]);

// Update FTP backup server with FTPS
$server = VirtualizorAdmin::backup()->update(4, [
    'hostname' => 'ftp.example.com',
    'name' => 'ftp-backup',
    'type' => 'FTP',
    'username' => 'ftpuser',
    'password' => 'newpass',
    'port' => 21,
    'ftps' => true
]);

// Get raw API response
$response = VirtualizorAdmin::backup()->update(4, [...], raw: true);

// Delete single backup server
$result = VirtualizorAdmin::backup()->delete(37);

// Delete multiple backup servers
$result = VirtualizorAdmin::backup()->delete([37, 36]);

// Get raw API response
$response = VirtualizorAdmin::backup()->delete(37, raw: true);

// Test backup server connectivity
$result = VirtualizorAdmin::backup()->test(1);

// Get raw API response
$response = VirtualizorAdmin::backup()->test(1, raw: true);
```

Backup Server List Response:
```php
[
    [
        'id' => 1,
        'type' => 'SSH',
        'name' => 'slate_doc_server',
        'hostname' => '192.168.1.123',
        'username' => 'root',
        'port' => 22,
        'directory' => '/tmp/backup'
    ],
    // ... more backup servers
]
```

Raw API Response:
```php
[
    'title' => 'Backup Servers',
    'backupservers' => [
        1 => [
            'bid' => '1',
            'type' => 'SSH',
            'name' => 'slate_doc_server',
            'hostname' => '192.168.1.123',
            'username' => 'root',
            'port' => '22',
            'dir' => '/tmp/backup'
        ]
    ],
    'timenow' => 1535553888,
    'time_taken' => '0.219'
]
```

Create Backup Server Response:
```php
[
    'success' => true,
    'id' => 2,
    'name' => 'backup-server-1',
    'type' => 'SSH',
    'hostname' => '192.168.1.100',
    'keys' => [  // Only present when gen_key is true
        'public_key' => 'ssh-rsa AAAAB3NzaC1...',
        'private_key' => '-----BEGIN RSA PRIVATE KEY-----...',
        'path' => '/var/virtualizor/ssh-keys/'
    ]
]
```

Raw API Response:
```php
[
    'title' => 'Add Backup Server',
    'done' => 2,  // backup server ID
    'keys' => [   // Only present when gen_key is true
        'public_key' => 'ssh-rsa AAAAB3NzaC1...',
        'private_key' => '-----BEGIN RSA PRIVATE KEY-----...',
        'path' => '/var/virtualizor/ssh-keys/'
    ],
    'timenow' => 1535547543,
    'time_taken' => '0.223'
]
```

Update Backup Server Response:
```php
[
    'success' => true,
    'id' => 4,
    'name' => 'lxc server',
    'type' => 'SSH',
    'hostname' => 'lxc.nuftp.com'
]
```

Raw API Response:
```php
[
    'title' => 'Edit Backup Server',
    'done' => true,
    'timenow' => '1477273778',
    'time_taken' => '0.078'
]
```

Delete Backup Server Response:
```php
[
    'success' => true,
    'deleted' => [
        6 => [
            'bid' => '6',
            'type' => 'SSH',
            'name' => 'backupstest1',
            'hostname' => 'testhost',
            'port' => '22',
            'dir' => ''
        ]
    ]
]
```

Raw API Response:
```php
[
    'title' => 'Backup Servers',
    'done' => [
        6 => [
            'bid' => '6',
            'type' => 'SSH',
            'name' => 'backupstest1',
            'hostname' => 'testhost',
            'port' => '22',
            'dir' => ''
        ]
    ],
    'backupservers' => null,
    'timenow' => 1535554537,
    'time_taken' => '0.221'
]
```

Test Backup Server Response:
```php
[
    'success' => true,
    'timestamp' => 1535557349
]
```

Raw API Response:
```php
[
    'title' => 'Backup Servers',
    'test_result' => 'success',
    'timenow' => 1535557349,
    'time_taken' => '0.361'
]
```

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

#### Virtual Server Management

```php
use CODEIQ\Virtualizor\VirtualizorAdmin;

// List all virtual servers
$servers = VirtualizorAdmin::vps()->list();

// List with filters
$servers = VirtualizorAdmin::vps()->list([
    'vpsid' => 123,                // optional: search by VPS ID
    'vpsname' => 'v1001',          // optional: search by VPS name
    'vpsip' => '8.8.8.1',         // optional: search by IP
    'vpshostname' => 'web1',       // optional: search by hostname
    'vsstatus' => 's',            // optional: 's' for suspended, 'u' for unsuspended
    'vstype' => 'kvm',            // optional: virtualization type
    'speedcap' => '1',            // optional: '1' for enabled, '2' for disabled
    'user' => 'john@example.com', // optional: search by user
    'vsgid' => [1, 2],           // optional: server group IDs
    'vserid' => [1],             // optional: server IDs
    'plid' => [1, 2],            // optional: plan IDs
    'bpid' => [1]                // optional: backup plan IDs
]);

// Create a VPS
$vps = VirtualizorAdmin::vps()->create([
    'virt' => 'kvm',              // required: virtualization type
    'user_email' => 'user@example.com', // required
    'user_pass' => 'secret',      // required
    'hostname' => 'server1.com',   // required
    'rootpass' => 'secret',       // required
    'osid' => 1,                  // required: OS template ID
    'ips' => ['192.168.1.100'],   // required
    'space' => 20,                // required: disk space in GB
    'ram' => 1024,                // required: RAM in MB
    'bandwidth' => 1000,          // required: bandwidth in GB
    'cores' => 2                  // required: CPU cores
]);

// Manage/Update a VPS
$result = VirtualizorAdmin::vps()->manage(123, [
    'hostname' => 'new-hostname.com',
    'rootpass' => 'new-password',
    'ram' => 2048,                // RAM in MB
    'cores' => 4,                 // CPU cores
    'space' => [                  // Disk configuration
        0 => [
            'size' => 30,         // Size in GB
            'st_uuid' => 'storage-uuid',
            'bus_driver' => 'virtio',
            'bus_driver_num' => 0
        ]
    ],
    'bandwidth' => 2000,          // Bandwidth in GB
    'vnc' => 1,                   // Enable VNC
    'vncpass' => 'vnc-password',  // VNC password
    'cpu_mode' => 'host-model',   // CPU mode
    'enable_rescue' => 1,         // Enable rescue mode
    'rescue_pass' => 'rescue123', // Rescue mode password
    'conf_rescue_pass' => 'rescue123' // Confirm rescue password
]);

// Delete a VPS
$result = VirtualizorAdmin::vps()->delete(123);

// Get raw API responses
$response = VirtualizorAdmin::vps()->list([], raw: true);
$response = VirtualizorAdmin::vps()->create($data, raw: true);
$response = VirtualizorAdmin::vps()->manage(123, $data, raw: true);
$response = VirtualizorAdmin::vps()->delete(123, raw: true);

// Start a VPS
$success = VirtualizorAdmin::vps()->start(123); // returns true/false

// Stop a VPS
$success = VirtualizorAdmin::vps()->stop(123); // returns true/false

// Restart a VPS
$success = VirtualizorAdmin::vps()->restart(123); // returns true/false

// Get raw API responses
$response = VirtualizorAdmin::vps()->start(123, raw: true);
$response = VirtualizorAdmin::vps()->stop(123, raw: true);
$response = VirtualizorAdmin::vps()->restart(123, raw: true);

// Power off a VPS
$success = VirtualizorAdmin::vps()->poweroff(123); // returns true/false

// Get raw API response
$response = VirtualizorAdmin::vps()->poweroff(123, raw: true);

// Suspend a VPS
$success = VirtualizorAdmin::vps()->suspend(123); // returns true/false

// Suspend with reason
$success = VirtualizorAdmin::vps()->suspend(123, 'Payment overdue');

// Unsuspend a VPS
$success = VirtualizorAdmin::vps()->unsuspend(123); // returns true/false

// Get raw API responses
$response = VirtualizorAdmin::vps()->suspend(123, 'Payment overdue', raw: true);
$response = VirtualizorAdmin::vps()->unsuspend(123, raw: true);

// Suspend network for a VPS
$success = VirtualizorAdmin::vps()->suspendNetwork(123); // returns true/false

// Unsuspend network for a VPS
$success = VirtualizorAdmin::vps()->unsuspendNetwork(123); // returns true/false

// Get raw API responses
$response = VirtualizorAdmin::vps()->suspendNetwork(123, raw: true);
$response = VirtualizorAdmin::vps()->unsuspendNetwork(123, raw: true);

// Rebuild a VPS with minimum parameters
$success = VirtualizorAdmin::vps()->rebuild(
    123,        // VPS ID
    270,        // OS template ID
    'newpass'   // New root password
);

// Rebuild with additional options
$success = VirtualizorAdmin::vps()->rebuild(
    123,        // VPS ID
    270,        // OS template ID
    'newpass',  // New root password
    [
        'format_primary' => true,     // Format only primary disk
        'send_email' => true,         // Send email to user
        'recipe' => 2,                // Recipe ID to apply
        'sshkey' => 'public_ssh_key'  // SSH key to add
    ]
);

// Get raw API response
$response = VirtualizorAdmin::vps()->rebuild(123, 270, 'newpass', [], raw: true);

// Migrate a single VPS
$success = VirtualizorAdmin::vps()->migrate(
    [123],              // VPS IDs
    [279],              // Storage IDs
    '192.168.1.100',    // Source server IP
    'server_password'    // Source server password
);

// Migrate multiple VPSes with options
$success = VirtualizorAdmin::vps()->migrate(
    [123, 124, 125],    // VPS IDs
    [279, 280, 281],    // Storage IDs
    '192.168.1.100',    // Source server IP
    'server_password',   // Source server password
    [
        'from_server' => 1,           // Source server ID
        'to_server' => 2,             // Destination server ID
        'speed_limit' => 100,         // Migration speed limit in Mb/s
        'preserve_ip' => true,        // Keep same IP addresses
        'delete_original' => true,    // Delete source VPS after migration
        'ignore_vdf_conflict' => true, // Ignore HAProxy VDF conflicts
        'disable_gzip' => false,      // Use compression
        'live_migration' => true      // Attempt live migration
    ]
);

// Get raw API response
$response = VirtualizorAdmin::vps()->migrate(
    [123], [279], '192.168.1.100', 'password', [], raw: true
);

// Clone a single VPS
$success = VirtualizorAdmin::vps()->clone(
    [180],              // VPS IDs
    [1, 5],            // Storage IDs
    0,                 // Source server ID
    0,                 // Destination server ID
    [
        'speed_limit' => 40  // Optional: Speed limit in Mb/s
    ]
);

// Clone with all options
$success = VirtualizorAdmin::vps()->clone(
    [180],              // VPS IDs
    [1, 5],            // Storage IDs
    0,                 // Source server ID
    0,                 // Destination server ID
    [
        'speed_limit' => 40,         // Speed limit in Mb/s
        'ignore_vdf_conflict' => true, // Ignore domain forwarding conflicts
        'disable_gzip' => false,      // Use compression
        'preserve_ip' => true         // Keep same IP addresses
    ]
);

// Get raw API response
$response = VirtualizorAdmin::vps()->clone(
    [180], [1, 5], 0, 0, [], raw: true
);

// Get VNC information for a VPS
$vncInfo = VirtualizorAdmin::vps()->getVncInfo(123);

// Get raw API response
$response = VirtualizorAdmin::vps()->getVncInfo(123, raw: true);

// Add domain forwarding record
$recordId = VirtualizorAdmin::vps()->addDomainForwarding(
    'tfri5lvqzl8lqhvt',    // VPS UUID
    'TCP',                  // Protocol (HTTP, HTTPS, TCP)
    '202.168.147.144',      // Source hostname/IP
    1224,                   // Source port
    '10.0.0.10',           // Destination IP
    22                      // Destination port
);

// Add with custom server ID
$recordId = VirtualizorAdmin::vps()->addDomainForwarding(
    'tfri5lvqzl8lqhvt',    // VPS UUID
    'HTTP',                 // Protocol
    'example.com',          // Source hostname
    80,                     // Source port
    '10.0.0.10',           // Destination IP
    8080,                   // Destination port
    1                      // Server ID
);

// Get raw API response
$response = VirtualizorAdmin::vps()->addDomainForwarding(
    'tfri5lvqzl8lqhvt', 'TCP', '202.168.147.144', 1224, '10.0.0.10', 22, 0, raw: true
);

// Edit domain forwarding record
$success = VirtualizorAdmin::vps()->editDomainForwarding(
    2,                    // Record ID
    'tfri5lvqzl8lqhvt',  // VPS UUID
    'TCP',                // Protocol (HTTP, HTTPS, TCP)
    '202.168.147.144',    // Source hostname/IP
    1226,                 // Source port
    '10.0.0.10',         // Destination IP
    8080                  // Destination port
);

// Edit with custom server ID
$success = VirtualizorAdmin::vps()->editDomainForwarding(
    2,                    // Record ID
    'tfri5lvqzl8lqhvt',  // VPS UUID
    'HTTP',               // Protocol
    'example.com',        // Source hostname
    80,                   // Source port
    '10.0.0.10',         // Destination IP
    8080,                 // Destination port
    1                    // Server ID
);

// Get raw API response
$response = VirtualizorAdmin::vps()->editDomainForwarding(
    2, 'tfri5lvqzl8lqhvt', 'TCP', '202.168.147.144', 1226, '10.0.0.10', 8080, 0, raw: true
);

// Delete a single domain forwarding record
$success = VirtualizorAdmin::vps()->deleteDomainForwarding(5);

// Delete multiple records
$success = VirtualizorAdmin::vps()->deleteDomainForwarding([2, 3, 5]);

// Get raw API response
$response = VirtualizorAdmin::vps()->deleteDomainForwarding(5, raw: true);

// List all domain forwarding records
$records = VirtualizorAdmin::vps()->listDomainForwarding();

// List with filters
$records = VirtualizorAdmin::vps()->listDomainForwarding([
    'record_id' => 7,              // Filter by record ID
    'server_id' => 1,              // Filter by server ID
    'vps_id' => 62,               // Filter by VPS ID
    'protocol' => 'TCP',          // Filter by protocol (TCP, HTTP, HTTPS)
    'source_hostname' => '202.168.147.144', // Filter by source hostname/IP
    'source_port' => 1226,        // Filter by source port
    'dest_ip' => '10.0.0.10',     // Filter by destination IP
    'dest_port' => 22             // Filter by destination port
]);

// Get raw API response
$response = VirtualizorAdmin::vps()->listDomainForwarding([], 1, 50, raw: true);

// Get High Availability status
$status = VirtualizorAdmin::vps()->getHaStatus();

// Get HA status for specific server group
$status = VirtualizorAdmin::vps()->getHaStatus(16);

// Get raw API response
$response = VirtualizorAdmin::vps()->getHaStatus(null, raw: true);

// Reset bandwidth for a VPS
$success = VirtualizorAdmin::vps()->resetBandwidth(123); // returns true/false

// Get raw API response
$response = VirtualizorAdmin::vps()->resetBandwidth(123, raw: true);

// Get status for a single VPS
$status = VirtualizorAdmin::vps()->status(123);

// Get status for multiple VPSes
$statuses = VirtualizorAdmin::vps()->status([123, 124, 125]);

// Get raw API response
$response = VirtualizorAdmin::vps()->status([123, 124], raw: true);

// Update network rules for a VPS
$success = VirtualizorAdmin::vps()->updateNetworkRules(123); // returns true/false

// Get raw API response
$response = VirtualizorAdmin::vps()->updateNetworkRules(123, raw: true);

// Import OS templates from SolusVM
$templates = VirtualizorAdmin::vps()->importSolusvm('os', [
    'changeserid' => 3,  // Optional: Server ID where SolusVM is installed
    'solusvm_os' => 1,   // Import OS templates
    'kvm_2' => '334',    // Map SolusVM template ID to Virtualizor template ID
    'kvm_5' => '334'     // Map another template
]);

// Import users from SolusVM
$users = VirtualizorAdmin::vps()->importSolusvm('users', [
    'solusvm_users' => 1  // Import users
]);

// Get raw API response
$response = VirtualizorAdmin::vps()->importSolusvm('os', [], raw: true);

// Import VPS from Proxmox
$vpsData = VirtualizorAdmin::vps()->importProxmox('vps', [
    'changeserid' => 3,     // Optional: Server ID where Proxmox is installed
    'proxmox_vps' => 1      // Import VPS
]);

// Import users from Proxmox
$users = VirtualizorAdmin::vps()->importProxmox('users', [
    'proxmox_users' => 1    // Import users
]);

// Get raw API response
$response = VirtualizorAdmin::vps()->importProxmox('vps', [], raw: true);

// Import VPS from Feathur
$vpsData = VirtualizorAdmin::vps()->importFeathur('vps', [
    'changeserid' => 3,     // Optional: Server ID where Feathur is installed
    'feathur_vps' => 1      // Import VPS
]);

// Import users from Feathur
$users = VirtualizorAdmin::vps()->importFeathur('users', [
    'feathur_users' => 1    // Import users
]);

// Get raw API response
$response = VirtualizorAdmin::vps()->importFeathur('vps', [], raw: true);

// Import VPS from HyperVM
$vpsData = VirtualizorAdmin::vps()->importHypervm('vps', [
    'changeserid' => 3,     // Optional: Server ID where HyperVM is installed
    'hypervm_vps' => 1      // Import VPS
]);

// Import users from HyperVM
$users = VirtualizorAdmin::vps()->importHypervm('users', [
    'hypervm_users' => 1    // Import users
]);

// Get raw API response
$response = VirtualizorAdmin::vps()->importHypervm('vps', [], raw: true);

// List importable OpenVZ VPSes
$vpsData = VirtualizorAdmin::vps()->importOpenvz(3); // Server ID 3

// Import specific OpenVZ VPSes
$result = VirtualizorAdmin::vps()->importOpenvz(3, [
    '121' => [          // VPS name/ID
        'bandwidth' => 2,
        'user_id' => 15
    ],
    '99013' => [        // Another VPS
        'bandwidth' => 2,
        'user_id' => 15
    ]
]);

// Get raw API response
$response = VirtualizorAdmin::vps()->importOpenvz(3, [], raw: true);

// List importable XEN Server VPSes
$vpsData = VirtualizorAdmin::vps()->importXenServer(3); // Server ID 3

// Import specific XEN Server VPSes
$result = VirtualizorAdmin::vps()->importXenServer(3, [
    'v1032' => [          // VPS name/ID
        'bandwidth' => 2,
        'user_id' => 15
    ],
    'v1025' => [        // Another VPS
        'bandwidth' => 2,
        'user_id' => 15
    ]
]);

// Get raw API response
$response = VirtualizorAdmin::vps()->importXenServer(3, [], raw: true);

// List importable OpenVZ 7 VPSes
$vpsData = VirtualizorAdmin::vps()->importOpenvz7(3); // Server ID 3

// Import specific OpenVZ 7 VPSes
$result = VirtualizorAdmin::vps()->importOpenvz7(3, [
    'v1032' => [          // VPS name/ID
        'bandwidth' => 2,
        'user_id' => 15
    ],
    'v1025' => [        // Another VPS
        'bandwidth' => 2,
        'user_id' => 15
    ]
]);

// Get raw API response
$response = VirtualizorAdmin::vps()->importOpenvz7(3, [], raw: true);

// List SSH keys for a user
$keys = VirtualizorAdmin::vps()->listSshKeys(123);

// Get raw API response
$response = VirtualizorAdmin::vps()->listSshKeys(123, raw: true);
```

Manage VPS Response:
```php
[
    'success' => true,
    'id' => 123,
    'vps' => [
        'vpsid' => '123',
        'vps_name' => 'v1001',
        'hostname' => 'new-hostname.com',
        'ram' => '2048',
        'cores' => '4',
        'bandwidth' => '2000',
        // ... other VPS properties
    ],
    'timestamp' => 1535123989
]
```

Raw API Response:
```php
{
    'title' => 'Manage VPS',
    'done' => true,
    'error' => [],
    'vs_info' => [
        'vpsid' => '123',
        'vps_name' => 'v1001',
        'uuid' => 'zou88fl1avyklu0s',
        // ... detailed VPS information
    ],
    'timenow' => 1535123989
}
```

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

#### Storage Management

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

#### VPS Statistics

```php
use CODEIQ\Virtualizor\VirtualizorAdmin;

// Get all VPS statistics
$stats = VirtualizorAdmin::vpsStats()->get();

// Get statistics for specific VPS
$stats = VirtualizorAdmin::vpsStats()->get([
    'vpsid' => 45
]);

// Get monthly statistics for specific VPS
$stats = VirtualizorAdmin::vpsStats()->get([
    'svs' => 45,              // VPS ID
    'show' => '202401'        // YYYYMM format
]);

// Get raw API response
$response = VirtualizorAdmin::vpsStats()->get(raw: true);
```

VPS Statistics Response:
```php
[
    'server' => [
        'bandwidth' => [
            'limit' => 5349.67,
            'used' => 5348.67,
            'usage' => [
                '1' => 0,
                '2' => 0,
                // ... daily usage
            ],
            'in' => [
                'usage' => [...],
                'used' => 4919.46,
                'limit' => 4920.46,
                'free' => 1
            ],
            'out' => [
                'usage' => [...],
                'used' => 429.2,
                'limit' => 430.2,
                'free' => 1
            ]
        ],
        'cpu' => [
            'manu' => 'intel',
            'cpumodel' => 'Intel(R) Pentium(R) CPU G620 @ 2.60GHz',
            'limit' => 2600,
            'used' => 2.6,
            'free' => 2597.4,
            'percent' => 0.1
        ],
        'ram' => [
            [
                'used_ram' => 806,
                'ram' => 1989
            ]
        ]
    ],
    'vps_data' => [
        '74' => [
            'status' => 1,
            'net_in' => 52,
            'net_out' => 0,
            'used_cpu' => '0.0',
            'used_ram' => '128',
            'ram' => '128',
            'used_disk' => 0.68,
            'disk' => '2',
            'used_bandwidth' => 0,
            'bandwidth' => 1,
            'virt' => 'kvm',
            'hostname' => 'www.mydomain.com'
        ]
    ],
    'timestamp' => 1536602202,
    'time_taken' => '2.156'
]
```

Monthly Statistics Response:
```php
[
    'vps_stats' => [
        [
            'vps_id' => 45,
            'timestamp' => 1570776006,
            'status' => true,
            'disk' => 0,
            'inode' => 0,
            'ram' => 0,
            'cpu' => [
                'usage' => 0.60,
                'actual' => 0.60
            ],
            'network' => [
                'in' => 1266,
                'out' => 0
            ],
            'io' => [
                'read' => 0,
                'write' => 0
            ]
        ]
    ],
    'month' => [
        'current_month' => '202401',
        'prev_month' => '202312',
        'next_month' => '202402'
    ],
    'timestamp' => 1570776103,
    'time_taken' => '0.076'
]
```

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