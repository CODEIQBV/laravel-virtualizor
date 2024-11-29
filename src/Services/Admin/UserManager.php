<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

class UserManager
{
    public function __construct(
        private readonly AdminApi $api
    ) {}

    /**
     * List all users
     */
    public function list(array $filters = [], int $page = 1, int $perPage = 50, bool $raw = false): array
    {
        $response = $this->api->users($page, $perPage, $filters);

        return $raw ? $response : ($response['users'] ?? []);
    }

    /**
     * Get a specific user by ID
     *
     * @throws \Exception if user not found
     */
    public function get(int $userId, bool $raw = false): array
    {
        // Use list endpoint with uid filter
        $response = $this->api->users(1, 1, ['uid' => $userId]);

        if ($raw) {
            return $response;
        }

        // Extract just the requested user from the users array
        $users = $response['users'] ?? [];

        if (! isset($users[$userId])) {
            throw new \Exception("User with ID {$userId} not found");
        }

        return $users[$userId];
    }

    /**
     * Create a new user
     *
     * @param array{
     *     priority: int,
     *     email: string,
     *     password: string,
     *     fname?: string,
     *     lname?: string,
     *     dnsplan_id?: int,
     *     uplid?: int,
     *     acl_id?: int,
     *     num_vs?: int,
     *     num_users?: int,
     *     space?: int,
     *     ram?: int,
     *     burst?: int,
     *     bandwidth?: int,
     *     cpu?: int,
     *     cores?: int,
     *     cpu_percent?: int,
     *     num_cores?: int,
     *     num_ipv4?: int,
     *     num_ipv6_subnet?: int,
     *     num_ipv6?: int,
     *     network_speed?: int,
     *     upload_speed?: int,
     *     band_suspend?: bool,
     *     service_period?: int,
     *     allowed_virts?: array,
     *     sgs?: array,
     *     mgs?: array,
     *     space_per_vm?: int,
     *     total_iops_sec?: int,
     *     read_bytes_sec?: int,
     *     write_bytes_sec?: int
     * } $userData
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function create(array $userData, bool $raw = false): array
    {
        // Validate required fields
        if (! isset($userData['priority'])) {
            throw new VirtualizorApiException('User priority is required (0: User, 1: Admin, 2: Cloud)');
        }
        if (! isset($userData['email'])) {
            throw new VirtualizorApiException('Email is required');
        }
        if (! isset($userData['password'])) {
            throw new VirtualizorApiException('Password is required');
        }

        // Format data for API
        $params = [
            'adduser' => 1,
            'priority' => $userData['priority'],
            'newemail' => $userData['email'],
            'newpass' => $userData['password'],
            'fname' => $userData['fname'] ?? '',
            'lname' => $userData['lname'] ?? '',
            'dnsplan_id' => $userData['dnsplan_id'] ?? 0,
            'uplid' => $userData['uplid'] ?? 0,
            'acl_id' => $userData['acl_id'] ?? 0,
            'num_vs' => $userData['num_vs'] ?? 0,
            'num_users' => $userData['num_users'] ?? 0,
            'space' => $userData['space'] ?? 0,
            'ram' => $userData['ram'] ?? 0,
            'burst' => $userData['burst'] ?? 0,
            'bandwidth' => $userData['bandwidth'] ?? 0,
            'cpu' => $userData['cpu'] ?? 0,
            'cores' => $userData['cores'] ?? 0,
            'cpu_percent' => $userData['cpu_percent'] ?? 0,
            'num_cores' => $userData['num_cores'] ?? 0,
            'num_ipv4' => $userData['num_ipv4'] ?? 0,
            'num_ipv6_subnet' => $userData['num_ipv6_subnet'] ?? 0,
            'num_ipv6' => $userData['num_ipv6'] ?? 0,
            'network_speed' => $userData['network_speed'] ?? 0,
            'upload_speed' => $userData['upload_speed'] ?? 0,
            'band_suspend' => isset($userData['band_suspend']) ? (int) $userData['band_suspend'] : 0,
            'service_period' => $userData['service_period'] ?? 1,
        ];

        // Optional arrays
        if (! empty($userData['allowed_virts'])) {
            $params['allowed_virts'] = $userData['allowed_virts'];
        }
        if (! empty($userData['sgs'])) {
            $params['sgs'] = $userData['sgs'];
        }
        if (! empty($userData['mgs'])) {
            $params['mgs'] = $userData['mgs'];
        }

        // Optional IOPS settings
        if (isset($userData['space_per_vm'])) {
            $params['space_per_vm'] = $userData['space_per_vm'];
        }
        if (isset($userData['total_iops_sec'])) {
            $params['total_iops_sec'] = $userData['total_iops_sec'];
        }
        if (isset($userData['read_bytes_sec'])) {
            $params['read_bytes_sec'] = $userData['read_bytes_sec'];
        }
        if (isset($userData['write_bytes_sec'])) {
            $params['write_bytes_sec'] = $userData['write_bytes_sec'];
        }

        $response = $this->api->adduser($params);

        if ($raw) {
            return $response;
        }

        // Return just the created user ID
        if (! empty($response['done'])) {
            return [
                'id' => (int) $response['done'],
                'email' => $userData['email'],
                'type' => $this->getUserType($userData['priority']),
            ];
        }

        throw new VirtualizorApiException('Failed to create user');
    }

    private function getUserType(int $priority): string
    {
        return match ($priority) {
            0 => 'user',
            1 => 'admin',
            2 => 'cloud',
            default => 'unknown'
        };
    }

    /**
     * Update an existing user
     *
     * @param array{
     *     priority?: int,
     *     email?: string,
     *     password?: string,
     *     fname?: string,
     *     lname?: string,
     *     dnsplan_id?: int,
     *     acl_id?: int,
     *     num_vs?: int,
     *     num_users?: int,
     *     space?: int,
     *     ram?: int,
     *     burst?: int,
     *     bandwidth?: int,
     *     cpu?: int,
     *     cores?: int,
     *     cpu_percent?: int,
     *     num_cores?: int,
     *     num_ipv4?: int,
     *     num_ipv6_subnet?: int,
     *     num_ipv6?: int,
     *     network_speed?: int,
     *     upload_speed?: int,
     *     band_suspend?: bool,
     *     service_period?: int,
     *     allowed_virts?: array,
     *     sgs?: array,
     *     mgs?: array,
     *     space_per_vm?: int,
     *     total_iops_sec?: int,
     *     read_bytes_sec?: int,
     *     write_bytes_sec?: int
     * } $userData
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function update(int $userId, array $userData, bool $raw = false): array
    {
        $params = [];

        // Map the input fields to API parameters
        if (isset($userData['priority'])) {
            $params['priority'] = $userData['priority'];
        }
        if (isset($userData['email'])) {
            $params['newemail'] = $userData['email'];
        }
        if (isset($userData['password'])) {
            $params['newpass'] = $userData['password'];
        }
        if (isset($userData['fname'])) {
            $params['fname'] = $userData['fname'];
        }
        if (isset($userData['lname'])) {
            $params['lname'] = $userData['lname'];
        }
        if (isset($userData['dnsplan_id'])) {
            $params['dnsplan_id'] = $userData['dnsplan_id'];
        }
        if (isset($userData['acl_id'])) {
            $params['acl_id'] = $userData['acl_id'];
        }
        if (isset($userData['num_vs'])) {
            $params['num_vs'] = $userData['num_vs'];
        }
        if (isset($userData['num_users'])) {
            $params['num_users'] = $userData['num_users'];
        }
        if (isset($userData['space'])) {
            $params['space'] = $userData['space'];
        }
        if (isset($userData['ram'])) {
            $params['ram'] = $userData['ram'];
        }
        if (isset($userData['burst'])) {
            $params['burst'] = $userData['burst'];
        }
        if (isset($userData['bandwidth'])) {
            $params['bandwidth'] = $userData['bandwidth'];
        }
        if (isset($userData['cpu'])) {
            $params['cpu'] = $userData['cpu'];
        }
        if (isset($userData['cores'])) {
            $params['cores'] = $userData['cores'];
        }
        if (isset($userData['cpu_percent'])) {
            $params['cpu_percent'] = $userData['cpu_percent'];
        }
        if (isset($userData['num_cores'])) {
            $params['num_cores'] = $userData['num_cores'];
        }
        if (isset($userData['num_ipv4'])) {
            $params['num_ipv4'] = $userData['num_ipv4'];
        }
        if (isset($userData['num_ipv6_subnet'])) {
            $params['num_ipv6_subnet'] = $userData['num_ipv6_subnet'];
        }
        if (isset($userData['num_ipv6'])) {
            $params['num_ipv6'] = $userData['num_ipv6'];
        }
        if (isset($userData['network_speed'])) {
            $params['network_speed'] = $userData['network_speed'];
        }
        if (isset($userData['upload_speed'])) {
            $params['upload_speed'] = $userData['upload_speed'];
        }
        if (isset($userData['band_suspend'])) {
            $params['band_suspend'] = (int) $userData['band_suspend'];
        }
        if (isset($userData['service_period'])) {
            $params['service_period'] = $userData['service_period'];
        }
        if (! empty($userData['allowed_virts'])) {
            $params['allowed_virts'] = $userData['allowed_virts'];
        }
        if (! empty($userData['sgs'])) {
            $params['sgs'] = $userData['sgs'];
        }
        if (! empty($userData['mgs'])) {
            $params['mgs'] = $userData['mgs'];
        }

        // Optional IOPS settings
        if (isset($userData['space_per_vm'])) {
            $params['space_per_vm'] = $userData['space_per_vm'];
        }
        if (isset($userData['total_iops_sec'])) {
            $params['total_iops_sec'] = $userData['total_iops_sec'];
        }
        if (isset($userData['read_bytes_sec'])) {
            $params['read_bytes_sec'] = $userData['read_bytes_sec'];
        }
        if (isset($userData['write_bytes_sec'])) {
            $params['write_bytes_sec'] = $userData['write_bytes_sec'];
        }

        try {
            $response = $this->api->edituser($userId, $params);

            if ($raw) {
                return $response;
            }

            // Check if update was successful
            if (! empty($response['done'])) {
                return $response['users'] ?? [];
            }

            throw new VirtualizorApiException('Failed to update user: Unknown error');
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to update user {$userId}: ".$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Delete one or more users
     *
     * @param  int|array  $userIds  Single user ID or array of user IDs
     * @param  bool  $deleteVms  Whether to delete associated VMs (default: false)
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function delete($userIds, bool $deleteVms = false, bool $raw = false): array
    {
        try {
            $response = $this->api->deleteUsers($userIds, $deleteVms);

            if ($raw) {
                return $response;
            }

            // Check if deletion was successful
            if (! empty($response['done'])) {
                return $response['done'];
            }

            throw new VirtualizorApiException('Failed to delete user(s): Unknown error');
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to delete user(s): '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Suspend one or more users
     *
     * @param  int|array  $userIds  Single user ID or array of user IDs
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function suspend($userIds, bool $raw = false): array
    {
        try {
            $response = $this->api->suspendUsers($userIds);

            if ($raw) {
                return $response;
            }

            // Check if suspension was successful
            if (! empty($response['done'])) {
                return [
                    'success' => true,
                    'users' => $response['users'] ?? [],
                ];
            }

            throw new VirtualizorApiException('Failed to suspend user(s): Unknown error');
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to suspend user(s): '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Unsuspend one or more users
     *
     * @param  int|array  $userIds  Single user ID or array of user IDs
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function unsuspend($userIds, bool $raw = false): array
    {
        try {
            $response = $this->api->unsuspendUsers($userIds);

            if ($raw) {
                return $response;
            }

            // Check if unsuspension was successful
            if (! empty($response['done'])) {
                return [
                    'success' => true,
                    'users' => $response['users'] ?? [],
                ];
            }

            throw new VirtualizorApiException('Failed to unsuspend user(s): Unknown error');
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to unsuspend user(s): '.$e->getMessage(),
                $e->getContext()
            );
        }
    }
}
