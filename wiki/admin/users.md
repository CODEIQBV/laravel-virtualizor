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
