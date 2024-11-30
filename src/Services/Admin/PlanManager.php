<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

class PlanManager
{
    /**
     * Available virtualization types
     */
    private const VIRT_TYPES = [
        'openvz', 'kvm', 'xcp', 'xcphvm', 'xen', 'xenhvm', 
        'lxc', 'vzo', 'vzk', 'proxo', 'proxk', 'proxl'
    ];

    /**
     * Available control panels
     */
    private const CONTROL_PANELS = [
        'cpanel', 'webuzo', 'plesk', 'webmin', 
        'interworx', 'ispconfig', 'cwp', 'vesta'
    ];

    /**
     * Available VNC keymaps
     */
    private const VNC_KEYMAPS = [
        'en-us', 'de-ch', 'ar', 'da', 'et', 'fo', 'fr-be', 
        'fr-ch', 'hu', 'it', 'lt', 'mk', 'nl', 'no', 'pt', 
        'ru', 'sv', 'tr', 'de', 'en-gb', 'es', 'fi', 'fr', 
        'fr-ca', 'hr', 'is', 'ja', 'lv', 'nl-be', 'pl', 
        'pt-br', 'sl', 'th'
    ];

    /**
     * User plan types
     */
    private const USER_PLAN_TYPES = [
        'NORMAL' => 0,
        'ADMIN' => 1,
        'CLOUD' => 2
    ];

    public function __construct(
        private readonly AdminApi $api
    ) {}

    /**
     * List plans
     *
     * @param array{
     *    planname?: string,
     *    ptype?: string
     * } $filters Search filters
     * @param int $page Page number
     * @param int $perPage Records per page
     * @param bool $raw Return raw API response
     * @return array Returns formatted plan info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function list(array $filters = [], int $page = 1, int $perPage = 50, bool $raw = false): array
    {
        try {
            // Validate virtualization type if provided
            if (isset($filters['ptype']) && !in_array($filters['ptype'], self::VIRT_TYPES)) {
                throw new VirtualizorApiException(
                    'Invalid virtualization type. Available types: ' . implode(', ', self::VIRT_TYPES)
                );
            }

            $response = $this->api->listPlans($filters, $page, $perPage);

            if ($raw) {
                return $response;
            }

            $plans = [];
            foreach ($response['plans'] ?? [] as $id => $plan) {
                $plans[] = [
                    'id' => (int) $plan['plid'],
                    'name' => $plan['plan_name'],
                    'virtualization' => $plan['virt'],
                    'resources' => [
                        'ips' => [
                            'ipv4' => (int) $plan['ips'],
                            'ipv6' => (int) $plan['ips6'],
                            'ipv6_subnets' => (int) $plan['ips6_subnet'],
                            'internal' => (int) $plan['ips_int']
                        ],
                        'storage' => [
                            'space' => (float) $plan['space'],
                            'inodes' => (int) $plan['inodes']
                        ],
                        'memory' => [
                            'ram' => (int) $plan['ram'],
                            'burst' => (int) $plan['burst'],
                            'swap' => (int) $plan['swap']
                        ],
                        'cpu' => [
                            'cpu_units' => (int) $plan['cpu'],
                            'cores' => (int) $plan['cores'],
                            'percent' => (float) $plan['cpu_percent'],
                            'mode' => $plan['cpu_mode'],
                            'topology' => [
                                'sockets' => (int) $plan['topology_sockets'],
                                'cores' => (int) $plan['topology_cores'],
                                'threads' => (int) $plan['topology_threads']
                            ]
                        ],
                        'network' => [
                            'bandwidth' => (float) $plan['bandwidth'],
                            'speed' => (int) $plan['network_speed'],
                            'upload_speed' => (int) $plan['upload_speed'],
                            'nic_type' => $plan['nic_type']
                        ],
                        'io' => [
                            'limit' => (int) $plan['io'],
                            'total_iops' => (int) $plan['total_iops_sec'],
                            'read_bytes' => (int) $plan['read_bytes_sec'],
                            'write_bytes' => (int) $plan['write_bytes_sec']
                        ]
                    ],
                    'settings' => [
                        'is_enabled' => (bool) $plan['is_enabled'],
                        'band_suspend' => (bool) $plan['band_suspend'],
                        'admin_managed' => (bool) $plan['admin_managed'],
                        'disable_nw_config' => (bool) $plan['disable_nw_config'],
                        'osreinstall_limit' => (int) $plan['osreinstall_limit'],
                        'control_panel' => $plan['control_panel'],
                        'vnc' => [
                            'enabled' => (bool) $plan['vnc'],
                            'keymap' => $plan['vnc_keymap']
                        ],
                        'rdp' => (bool) $plan['rdp'],
                        'recipe' => (int) $plan['recipe'],
                        'backup_plan' => (int) $plan['bpid']
                    ]
                ];
            }

            return [
                'plans' => $plans,
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to list plans: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Create a new plan
     *
     * @param array $params Plan parameters
     * @param bool $raw Return raw API response
     * @return array|int Returns plan ID when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function create(array $params, bool $raw = false): array|int
    {
        try {
            // Validate required fields
            $required = ['plan_name', 'cplan', 'disk_space', 'guaranteed_ram', 'swapram', 
                        'bandwidth', 'cpu_units', 'cpu_cores', 'percent_cpu'];
            foreach ($required as $field) {
                if (!isset($params[$field])) {
                    throw new VirtualizorApiException("{$field} is required");
                }
            }

            // Validate virtualization type
            if (!in_array($params['cplan'], self::VIRT_TYPES)) {
                throw new VirtualizorApiException(
                    'Invalid virtualization type. Available types: ' . implode(', ', self::VIRT_TYPES)
                );
            }

            // Validate control panel if provided
            if (isset($params['control_panel']) && !in_array($params['control_panel'], self::CONTROL_PANELS)) {
                throw new VirtualizorApiException(
                    'Invalid control panel. Available panels: ' . implode(', ', self::CONTROL_PANELS)
                );
            }

            // Validate VNC keymap if provided
            if (isset($params['vnc_keymap']) && !in_array($params['vnc_keymap'], self::VNC_KEYMAPS)) {
                throw new VirtualizorApiException(
                    'Invalid VNC keymap. Available keymaps: ' . implode(', ', self::VNC_KEYMAPS)
                );
            }

            // Validate OpenVZ specific requirements
            if ($params['cplan'] === 'openvz' && !isset($params['burst_ram'])) {
                throw new VirtualizorApiException('burst_ram is required for OpenVZ plans');
            }

            $response = $this->api->addPlan($params);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to create plan: Operation unsuccessful'
                );
            }

            return (int) $response['done'];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to create plan: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Edit a plan
     *
     * @param int $planId Plan ID to edit
     * @param array $params Plan parameters
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function edit(int $planId, array $params, bool $raw = false): array|bool
    {
        try {
            // Validate required fields
            $required = ['plan_name', 'cplan', 'disk_space', 'guaranteed_ram', 'swapram', 
                        'bandwidth', 'cpu_units', 'cpu_cores', 'percent_cpu'];
            foreach ($required as $field) {
                if (!isset($params[$field])) {
                    throw new VirtualizorApiException("{$field} is required");
                }
            }

            // Validate virtualization type
            if (!in_array($params['cplan'], self::VIRT_TYPES)) {
                throw new VirtualizorApiException(
                    'Invalid virtualization type. Available types: ' . implode(', ', self::VIRT_TYPES)
                );
            }

            // Validate control panel if provided
            if (isset($params['control_panel']) && !in_array($params['control_panel'], self::CONTROL_PANELS)) {
                throw new VirtualizorApiException(
                    'Invalid control panel. Available panels: ' . implode(', ', self::CONTROL_PANELS)
                );
            }

            // Validate VNC keymap if provided
            if (isset($params['vnc_keymap']) && !in_array($params['vnc_keymap'], self::VNC_KEYMAPS)) {
                throw new VirtualizorApiException(
                    'Invalid VNC keymap. Available keymaps: ' . implode(', ', self::VNC_KEYMAPS)
                );
            }

            // Validate OpenVZ specific requirements
            if ($params['cplan'] === 'openvz' && !isset($params['burst_ram'])) {
                throw new VirtualizorApiException('burst_ram is required for OpenVZ plans');
            }

            $response = $this->api->editPlan($planId, $params);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to edit plan: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to edit plan {$planId}: " . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Delete plan(s)
     *
     * @param int|array $planIds Single plan ID or array of plan IDs
     * @param bool $raw Return raw API response
     * @return array Returns deleted plan info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function delete(int|array $planIds, bool $raw = false): array
    {
        try {
            $response = $this->api->deletePlans($planIds);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to delete plan: Operation unsuccessful'
                );
            }

            // Format the deleted plan information
            $deletedPlans = [];
            foreach ($response['done'] as $id => $plan) {
                $deletedPlans[$id] = [
                    'id' => (int) $plan['plid'],
                    'name' => $plan['plan_name'],
                    'virtualization' => $plan['virt'],
                    'resources' => [
                        'ips' => [
                            'ipv4' => (int) $plan['ips'],
                            'ipv6' => (int) $plan['ips6'],
                            'ipv6_subnets' => (int) $plan['ips6_subnet'],
                            'internal' => (int) $plan['ips_int']
                        ],
                        'storage' => [
                            'space' => (float) $plan['space'],
                            'inodes' => (int) $plan['inodes']
                        ],
                        'memory' => [
                            'ram' => (int) $plan['ram'],
                            'burst' => (int) $plan['burst'],
                            'swap' => (int) $plan['swap']
                        ],
                        'cpu' => [
                            'cpu_units' => (int) $plan['cpu'],
                            'cores' => (int) $plan['cores'],
                            'percent' => (float) $plan['cpu_percent']
                        ]
                    ],
                    'settings' => [
                        'is_enabled' => (bool) $plan['is_enabled'],
                        'band_suspend' => (bool) $plan['band_suspend'],
                        'admin_managed' => (bool) $plan['admin_managed']
                    ]
                ];
            }

            return [
                'deleted_plans' => $deletedPlans,
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            $ids = is_array($planIds) ? implode(', ', $planIds) : $planIds;
            throw new VirtualizorApiException(
                "Failed to delete plan(s) {$ids}: " . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * List user plans
     *
     * @param array{
     *    planname?: string,
     *    ptype?: string
     * } $filters Search filters
     * @param int $page Page number
     * @param int $perPage Records per page
     * @param bool $raw Return raw API response
     * @return array Returns formatted user plan info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function listUserPlans(array $filters = [], int $page = 1, int $perPage = 50, bool $raw = false): array
    {
        try {
            // Validate virtualization type if provided
            if (isset($filters['ptype']) && !in_array($filters['ptype'], self::VIRT_TYPES)) {
                throw new VirtualizorApiException(
                    'Invalid virtualization type. Available types: ' . implode(', ', self::VIRT_TYPES)
                );
            }

            $response = $this->api->listUserPlans($filters, $page, $perPage);

            if ($raw) {
                return $response;
            }

            $plans = [];
            foreach ($response['plans'] ?? [] as $id => $plan) {
                $plans[] = [
                    'id' => (int) $plan['uplid'],
                    'name' => $plan['plan_name'],
                    'type' => (int) $plan['type'],
                    'acl_id' => (int) $plan['aclid'],
                    'billing' => [
                        'inhouse' => (bool) $plan['inhouse_billing'],
                        'max_cost' => (float) $plan['max_cost']
                    ],
                    'limits' => [
                        'vps' => (int) $plan['num_vs'],
                        'users' => (int) $plan['num_users'],
                        'space' => (float) $plan['space'],
                        'ram' => (int) $plan['ram'],
                        'burst' => (int) $plan['burst'],
                        'bandwidth' => (float) $plan['bandwidth'],
                        'cpu' => [
                            'units' => (int) $plan['cpu'],
                            'cores' => (int) $plan['cores'],
                            'percent' => (float) $plan['cpu_percent'],
                            'num_cores' => (int) $plan['num_cores']
                        ],
                        'ips' => [
                            'ipv4' => (int) $plan['num_ipv4'],
                            'ipv6' => (int) $plan['num_ipv6'],
                            'ipv6_subnets' => (int) $plan['num_ipv6_subnet']
                        ],
                        'network' => [
                            'speed' => (int) $plan['network_speed'],
                            'upload_speed' => (int) $plan['upload_speed']
                        ]
                    ],
                    'settings' => [
                        'allowed_virts' => empty($plan['allowed_virts']) ? [] : explode(',', $plan['allowed_virts']),
                        'server_groups' => empty($plan['sg']) ? [] : explode(',', $plan['sg']),
                        'media_groups' => empty($plan['mg']) ? [] : explode(',', $plan['mg']),
                        'dns_plan_id' => (int) $plan['dnsplid'],
                        'service_period' => (int) $plan['service_period'],
                        'band_suspend' => (bool) $plan['band_suspend']
                    ],
                    'created_at' => (int) $plan['date_created']
                ];
            }

            return [
                'plans' => $plans,
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to list user plans: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Create a new user plan
     *
     * @param array{
     *    plan_name: string,
     *    priority: int,
     *    dnsplan_id?: int,
     *    acl_id?: int,
     *    num_vs?: int,
     *    inhouse_billing?: bool,
     *    num_users?: int,
     *    space?: int,
     *    ram?: int,
     *    burst?: int,
     *    bandwidth?: int,
     *    cpu?: int,
     *    cores?: int,
     *    cpu_percent?: int,
     *    num_cores?: int,
     *    num_ipv4?: int,
     *    num_ipv6_subnet?: int,
     *    num_ipv6?: int,
     *    network_speed?: int,
     *    upload_speed?: int,
     *    band_suspend?: bool,
     *    service_period?: int,
     *    allowed_virts?: array,
     *    sgs?: array,
     *    mgs?: array,
     *    space_per_vm?: int,
     *    total_iops_sec?: int,
     *    read_bytes_sec?: int,
     *    write_bytes_sec?: int
     * } $params Plan parameters
     * @param bool $raw Return raw API response
     * @return array|int Returns plan ID when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function createUserPlan(array $params, bool $raw = false): array|int
    {
        try {
            // Validate required fields
            if (empty($params['plan_name'])) {
                throw new VirtualizorApiException('plan_name is required');
            }

            if (!isset($params['priority'])) {
                throw new VirtualizorApiException('priority is required');
            }

            // Validate plan type
            if (!in_array($params['priority'], self::USER_PLAN_TYPES)) {
                throw new VirtualizorApiException(
                    'Invalid priority value. Available types: NORMAL (0), ADMIN (1), CLOUD (2)'
                );
            }

            // Validate virtualization types if provided
            if (!empty($params['allowed_virts'])) {
                foreach ($params['allowed_virts'] as $virt) {
                    if (!in_array($virt, self::VIRT_TYPES)) {
                        throw new VirtualizorApiException(
                            'Invalid virtualization type. Available types: ' . implode(', ', self::VIRT_TYPES)
                        );
                    }
                }
            }

            // Convert boolean values to integers
            if (isset($params['inhouse_billing'])) {
                $params['inhouse_billing'] = $params['inhouse_billing'] ? 1 : 0;
            }

            if (isset($params['band_suspend'])) {
                $params['band_suspend'] = $params['band_suspend'] ? 1 : 0;
            }

            // Validate service period
            if (isset($params['service_period']) && 
                ($params['service_period'] < 0 || $params['service_period'] > 31)) {
                throw new VirtualizorApiException('service_period must be between 0 and 31');
            }

            $response = $this->api->addUserPlan($params);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to create user plan: Operation unsuccessful'
                );
            }

            return (int) $response['done'];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to create user plan: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Edit a user plan
     *
     * @param int $planId User plan ID to edit
     * @param array{
     *    plan_name: string,
     *    priority: int,
     *    dnsplan_id?: int,
     *    acl_id?: int,
     *    num_vs?: int,
     *    inhouse_billing?: bool,
     *    num_users?: int,
     *    space?: int,
     *    ram?: int,
     *    burst?: int,
     *    bandwidth?: int,
     *    cpu?: int,
     *    cores?: int,
     *    cpu_percent?: int,
     *    num_cores?: int,
     *    num_ipv4?: int,
     *    num_ipv6_subnet?: int,
     *    num_ipv6?: int,
     *    network_speed?: int,
     *    upload_speed?: int,
     *    band_suspend?: bool,
     *    service_period?: int,
     *    allowed_virts?: array,
     *    sgs?: array,
     *    mgs?: array,
     *    space_per_vm?: int,
     *    total_iops_sec?: int,
     *    read_bytes_sec?: int,
     *    write_bytes_sec?: int
     * } $params Plan parameters
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function editUserPlan(int $planId, array $params, bool $raw = false): array|bool
    {
        try {
            // Validate required fields
            if (empty($params['plan_name'])) {
                throw new VirtualizorApiException('plan_name is required');
            }

            if (!isset($params['priority'])) {
                throw new VirtualizorApiException('priority is required');
            }

            // Validate plan type
            if (!in_array($params['priority'], self::USER_PLAN_TYPES)) {
                throw new VirtualizorApiException(
                    'Invalid priority value. Available types: NORMAL (0), ADMIN (1), CLOUD (2)'
                );
            }

            // Validate virtualization types if provided
            if (!empty($params['allowed_virts'])) {
                foreach ($params['allowed_virts'] as $virt) {
                    if (!in_array($virt, self::VIRT_TYPES)) {
                        throw new VirtualizorApiException(
                            'Invalid virtualization type. Available types: ' . implode(', ', self::VIRT_TYPES)
                        );
                    }
                }
            }

            // Convert boolean values to integers
            if (isset($params['inhouse_billing'])) {
                $params['inhouse_billing'] = $params['inhouse_billing'] ? 1 : 0;
            }

            if (isset($params['band_suspend'])) {
                $params['band_suspend'] = $params['band_suspend'] ? 1 : 0;
            }

            // Validate service period
            if (isset($params['service_period']) && 
                ($params['service_period'] < 0 || $params['service_period'] > 31)) {
                throw new VirtualizorApiException('service_period must be between 0 and 31');
            }

            $response = $this->api->editUserPlan($planId, $params);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to edit user plan: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to edit user plan {$planId}: " . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Delete user plan(s)
     *
     * @param int|array $planIds Single plan ID or array of plan IDs
     * @param bool $raw Return raw API response
     * @return array Returns deleted plan info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function deleteUserPlan(int|array $planIds, bool $raw = false): array
    {
        try {
            $response = $this->api->deleteUserPlans($planIds);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to delete user plan: Operation unsuccessful'
                );
            }

            // Format the deleted plan information
            $deletedPlans = [];
            foreach ($response['done'] as $id => $plan) {
                $deletedPlans[$id] = [
                    'id' => (int) $plan['uplid'],
                    'name' => $plan['plan_name'],
                    'type' => (int) $plan['type'],
                    'acl_id' => (int) $plan['aclid'],
                    'billing' => [
                        'inhouse' => (bool) $plan['inhouse_billing'],
                        'max_cost' => (float) $plan['max_cost']
                    ],
                    'limits' => [
                        'vps' => (int) $plan['num_vs'],
                        'users' => (int) $plan['num_users'],
                        'space' => (float) $plan['space'],
                        'ram' => (int) $plan['ram'],
                        'burst' => (int) $plan['burst'],
                        'bandwidth' => (float) $plan['bandwidth'],
                        'cpu' => [
                            'units' => (int) $plan['cpu'],
                            'cores' => (int) $plan['cores'],
                            'percent' => (float) $plan['cpu_percent'],
                            'num_cores' => (int) $plan['num_cores']
                        ],
                        'ips' => [
                            'ipv4' => (int) $plan['num_ipv4'],
                            'ipv6' => (int) $plan['num_ipv6'],
                            'ipv6_subnets' => (int) $plan['num_ipv6_subnet']
                        ],
                        'network' => [
                            'speed' => (int) $plan['network_speed'],
                            'upload_speed' => (int) $plan['upload_speed']
                        ]
                    ],
                    'settings' => [
                        'allowed_virts' => empty($plan['allowed_virts']) ? [] : explode(',', $plan['allowed_virts']),
                        'server_groups' => empty($plan['sg']) ? [] : explode(',', $plan['sg']),
                        'media_groups' => empty($plan['mg']) ? [] : explode(',', $plan['mg']),
                        'dns_plan_id' => (int) $plan['dnsplid'],
                        'service_period' => (int) $plan['service_period'],
                        'band_suspend' => (bool) $plan['band_suspend']
                    ],
                    'created_at' => (int) $plan['date_created']
                ];
            }

            return [
                'deleted_plans' => $deletedPlans,
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            $ids = is_array($planIds) ? implode(', ', $planIds) : $planIds;
            throw new VirtualizorApiException(
                "Failed to delete user plan(s) {$ids}: " . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    private function getVirtType(string $type): string 
    {
        return match($type) {
            'openvz', 'vzo' => 'OpenVZ',
            'kvm', 'vzk' => 'KVM',
            'xen' => 'Xen PV',
            'xenhvm' => 'Xen HVM',
            'lxc' => 'LXC',
            default => ucfirst($type)
        };
    }
} 