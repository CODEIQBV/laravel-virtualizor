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
