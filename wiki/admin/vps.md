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
