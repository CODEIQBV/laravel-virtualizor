<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

class IpPoolManager
{
    public function __construct(
        private readonly AdminApi $api
    ) {}

    /**
     * List IP pools
     *
     * @param  int  $page  Page number
     * @param  int  $perPage  Records per page
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function list(int $page = 1, int $perPage = 50, bool $raw = false): array
    {
        try {
            $response = $this->api->listIpPools($page, $perPage);

            if ($raw) {
                return $response;
            }

            return array_map(function ($pool) {
                return [
                    'id' => (int) $pool['ippid'],
                    'server_id' => (int) $pool['ipp_serid'],
                    'name' => $pool['ippool_name'],
                    'gateway' => $pool['gateway'],
                    'netmask' => $pool['netmask'],
                    'nameservers' => [
                        'ns1' => $pool['ns1'],
                        'ns2' => $pool['ns2'],
                    ],
                    'is_ipv6' => (bool) $pool['ipv6'],
                    'nat' => [
                        'enabled' => (bool) $pool['nat'],
                        'name' => $pool['nat_name'],
                    ],
                    'routing' => (bool) $pool['routing'],
                    'internal' => (bool) $pool['internal'],
                    'bridge' => $pool['bridge'],
                    'mtu' => (int) $pool['mtu'],
                    'vlan' => (int) $pool['vlan'],
                    'ips' => [
                        'total' => (int) $pool['totalip'],
                        'free' => (int) $pool['freeip'],
                    ],
                ];
            }, $response['ippools'] ?? []);
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to list IP pools: '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Create a new IP pool
     *
     * @param array{
     *    iptype: int,
     *    serid: int|array,
     *    ippool_name: string,
     *    gateway: string,
     *    netmask: string,
     *    ns1: string,
     *    ns2: string,
     *    firstip: string,
     *    lastip: string,
     *    nat?: bool,
     *    ips?: array,
     *    macs?: array,
     *    routing?: bool,
     *    internal?: bool,
     *    internal_bridge?: string,
     *    vlan?: bool,
     *    vlan_bridge?: string,
     *    mtu?: int,
     *    uid?: int
     * } $poolData IP pool creation data
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function create(array $poolData, bool $raw = false): array
    {
        // Validate required fields
        $required = ['iptype', 'serid', 'ippool_name', 'gateway', 'netmask', 'ns1', 'ns2', 'firstip', 'lastip'];
        foreach ($required as $field) {
            if (! isset($poolData[$field])) {
                throw new VirtualizorApiException("$field is required");
            }
        }

        // Validate IP type
        if (! in_array($poolData['iptype'], [4, 6])) {
            throw new VirtualizorApiException('Invalid IP type. Must be 4 for IPv4 or 6 for IPv6');
        }

        // Convert boolean values
        foreach (['nat', 'routing', 'internal', 'vlan'] as $field) {
            if (isset($poolData[$field])) {
                $poolData[$field] = $poolData[$field] ? 1 : 0;
            }
        }

        try {
            $response = $this->api->addIpPool($poolData);

            if ($raw) {
                return $response;
            }

            if (! empty($response['done'])) {
                return [
                    'success' => true,
                    'id' => (int) $response['done']['ippid'],
                    'name' => $poolData['ippool_name'],
                    'ip_range' => $response['done']['range'] ?? [],
                ];
            }

            throw new VirtualizorApiException('Failed to create IP pool: Unknown error');
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to create IP pool: '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Create a new internal IP pool
     *
     * @param array{
     *    iptype: int,
     *    serid: int|array,
     *    ippool_name: string,
     *    gateway: string,
     *    netmask: string,
     *    ns1: string,
     *    ns2: string,
     *    firstip: string,
     *    lastip: string,
     *    internal_bridge: string,
     *    mtu?: int,
     *    ips?: array,
     *    macs?: array
     * } $poolData Internal IP pool creation data
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function createInternal(array $poolData, bool $raw = false): array
    {
        // Force internal pool settings
        $poolData['internal'] = 1;

        // Validate required fields
        $required = ['iptype', 'serid', 'ippool_name', 'gateway', 'netmask', 'ns1', 'ns2', 'firstip', 'lastip', 'internal_bridge'];
        foreach ($required as $field) {
            if (! isset($poolData[$field])) {
                throw new VirtualizorApiException("$field is required for internal IP pool");
            }
        }

        return $this->create($poolData, $raw);
    }

    /**
     * Create a new IPv6 pool
     *
     * @param array{
     *    serid: int|array,
     *    ippool_name: string,
     *    gateway: string,
     *    netmask: string,
     *    ns1: string,
     *    ns2: string,
     *    ipv6_1: string,
     *    ipv6_2: string,
     *    ipv6_3: string,
     *    ipv6_4: string,
     *    ipv6_5: string,
     *    ipv6_6: string,
     *    ipv6_num: int,
     *    routing?: bool,
     *    vlan?: bool,
     *    vlan_bridge?: string,
     *    mtu?: int
     * } $poolData IPv6 pool creation data
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function createIpv6(array $poolData, bool $raw = false): array
    {
        // Force IPv6 type
        $poolData['iptype'] = 6;

        // Validate required IPv6 fields
        $required = [
            'serid', 'ippool_name', 'gateway', 'netmask', 'ns1', 'ns2',
            'ipv6_1', 'ipv6_2', 'ipv6_3', 'ipv6_4', 'ipv6_5', 'ipv6_6', 'ipv6_num',
        ];
        foreach ($required as $field) {
            if (! isset($poolData[$field])) {
                throw new VirtualizorApiException("$field is required for IPv6 pool");
            }
        }

        // Convert boolean values
        foreach (['routing', 'vlan'] as $field) {
            if (isset($poolData[$field])) {
                $poolData[$field] = $poolData[$field] ? 1 : 0;
            }
        }

        try {
            $response = $this->api->addIpPool($poolData);

            if ($raw) {
                return $response;
            }

            if (! empty($response['done'])) {
                return [
                    'success' => true,
                    'id' => (int) $response['done']['ippid'],
                    'name' => $poolData['ippool_name'],
                    'ip_range' => $response['done']['range'] ?? [],
                    'ipv6_subnet' => $response['done']['ipv6_subnet'] ?? null,
                ];
            }

            throw new VirtualizorApiException('Failed to create IPv6 pool: Unknown error');
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to create IPv6 pool: '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Edit an IP pool
     *
     * @param array{
     *    ippool_name: string,
     *    gateway: string,
     *    netmask: string,
     *    ns1: string,
     *    ns2: string,
     *    serid: int,
     *    nat?: bool,
     *    routing?: bool,
     *    mtu?: int,
     *    uid?: int
     * } $poolData IP pool update data
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function update(int $poolId, array $poolData, bool $raw = false): array
    {
        // Validate required fields
        $required = ['ippool_name', 'gateway', 'netmask', 'ns1', 'ns2', 'serid'];
        foreach ($required as $field) {
            if (! isset($poolData[$field])) {
                throw new VirtualizorApiException("$field is required");
            }
        }

        // Convert boolean values
        foreach (['nat', 'routing'] as $field) {
            if (isset($poolData[$field])) {
                $poolData[$field] = $poolData[$field] ? 1 : 0;
            }
        }

        try {
            $response = $this->api->editIpPool($poolId, $poolData);

            if ($raw) {
                return $response;
            }

            if (! empty($response['done'])) {
                return [
                    'success' => true,
                    'id' => $poolId,
                    'pool' => $response['ippool'] ?? [],
                    'servers' => $response['ippool_servers'] ?? [],
                ];
            }

            throw new VirtualizorApiException('Failed to update IP pool: Unknown error');
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to update IP pool {$poolId}: ".$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Delete one or more IP pools
     *
     * @param  int|array  $poolIds  Single pool ID or array of pool IDs
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function delete($poolIds, bool $raw = false): array
    {
        try {
            $response = $this->api->deleteIpPools($poolIds);

            if ($raw) {
                return $response;
            }

            if (! empty($response['done'])) {
                return [
                    'success' => true,
                    'deleted' => $response['done'],
                ];
            }

            throw new VirtualizorApiException('Failed to delete IP pool(s): Unknown error');
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to delete IP pool(s): '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * List all IP addresses
     *
     * @param  int  $page  Page number
     * @param  int  $perPage  Records per page
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function listIps(int $page = 1, int $perPage = 50, bool $raw = false): array
    {
        try {
            $response = $this->api->listIps($page, $perPage);

            if ($raw) {
                return $response;
            }

            return array_map(function ($ip) {
                return [
                    'id' => (int) $ip['ipid'],
                    'pool_id' => (int) $ip['ippid'],
                    'server_id' => (int) $ip['ip_serid'],
                    'vps_id' => (int) $ip['vpsid'],
                    'address' => $ip['ip'],
                    'is_ipv6' => (bool) $ip['ipv6'],
                    'netmask' => $ip['ipr_netmask'],
                    'is_primary' => (bool) $ip['primary'],
                    'mac_address' => $ip['mac_addr'],
                    'is_locked' => (bool) $ip['locked'],
                    'note' => $ip['note'],
                    'pool' => [
                        'name' => $ip['ippool_name'],
                        'gateway' => $ip['gateway'],
                        'netmask' => $ip['netmask'],
                        'nameservers' => [
                            'ns1' => $ip['ns1'],
                            'ns2' => $ip['ns2'],
                        ],
                        'nat' => [
                            'enabled' => (bool) $ip['nat'],
                            'name' => $ip['nat_name'],
                        ],
                        'routing' => (bool) $ip['routing'],
                        'internal' => (bool) $ip['internal'],
                        'bridge' => $ip['bridge'],
                        'mtu' => (int) $ip['mtu'],
                        'vlan' => (int) $ip['vlan'],
                    ],
                    'hostname' => $ip['hostname'],
                ];
            }, $response['ips'] ?? []);
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to list IPs: '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Search IP pools
     *
     * @param array{
     *    poolname?: string,
     *    poolgateway?: string,
     *    netmask?: string,
     *    nameserver?: string,
     *    servers_search?: int
     * } $filters Search filters
     * @param  int  $page  Page number
     * @param  int  $perPage  Records per page
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function search(array $filters = [], int $page = 1, int $perPage = 50, bool $raw = false): array
    {
        try {
            $response = $this->api->searchIpPools($filters, $page, $perPage);

            if ($raw) {
                return $response;
            }

            return array_map(function ($pool) {
                return [
                    'id' => (int) $pool['ippid'],
                    'server_id' => (int) $pool['ipp_serid'],
                    'name' => $pool['ippool_name'],
                    'gateway' => $pool['gateway'],
                    'netmask' => $pool['netmask'],
                    'nameservers' => [
                        'ns1' => $pool['ns1'],
                        'ns2' => $pool['ns2'],
                    ],
                    'is_ipv6' => (bool) $pool['ipv6'],
                    'nat' => [
                        'enabled' => (bool) $pool['nat'],
                        'name' => $pool['nat_name'],
                    ],
                    'routing' => (bool) $pool['routing'],
                    'internal' => (bool) $pool['internal'],
                    'bridge' => $pool['bridge'],
                    'mtu' => (int) $pool['mtu'],
                    'vlan' => (int) $pool['vlan'],
                    'ips' => [
                        'total' => (int) $pool['totalip'],
                        'free' => (int) $pool['freeip'],
                    ],
                ];
            }, $response['ippools'] ?? []);
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to search IP pools: '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Search IP addresses
     *
     * @param array{
     *    ipsearch?: string,
     *    ippoolsearch?: string,
     *    ippid?: int,
     *    macsearch?: string,
     *    vps_search?: string,
     *    servers_search?: int,
     *    lockedsearch?: string
     * } $filters Search filters
     * @param  int  $page  Page number
     * @param  int  $perPage  Records per page
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function searchIps(array $filters = [], int $page = 1, int $perPage = 50, bool $raw = false): array
    {
        try {
            $response = $this->api->searchIps($filters, $page, $perPage);

            if ($raw) {
                return $response;
            }

            return array_map(function ($ip) {
                return [
                    'id' => (int) $ip['ipid'],
                    'pool_id' => (int) $ip['ippid'],
                    'server_id' => (int) $ip['ip_serid'],
                    'vps_id' => (int) $ip['vpsid'],
                    'address' => $ip['ip'],
                    'is_ipv6' => (bool) $ip['ipv6'],
                    'netmask' => $ip['ipr_netmask'],
                    'is_primary' => (bool) $ip['primary'],
                    'mac_address' => $ip['mac_addr'],
                    'is_locked' => (bool) $ip['locked'],
                    'note' => $ip['note'],
                    'pool' => [
                        'name' => $ip['ippool_name'],
                        'gateway' => $ip['gateway'],
                        'netmask' => $ip['netmask'],
                        'nameservers' => [
                            'ns1' => $ip['ns1'],
                            'ns2' => $ip['ns2'],
                        ],
                        'nat' => [
                            'enabled' => (bool) $ip['nat'],
                            'name' => $ip['nat_name'],
                        ],
                        'routing' => (bool) $ip['routing'],
                        'internal' => (bool) $ip['internal'],
                        'bridge' => $ip['bridge'],
                        'mtu' => (int) $ip['mtu'],
                        'vlan' => (int) $ip['vlan'],
                    ],
                    'hostname' => $ip['hostname'],
                ];
            }, $response['ips'] ?? []);
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to search IPs: '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Add IPv4 addresses
     *
     * @param array{
     *    ips?: array,
     *    macs?: array,
     *    firstip?: string,
     *    lastip?: string,
     *    ippid?: int,
     *    ip_serid?: int
     * } $ipData IP address data
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function addIpv4(array $ipData, bool $raw = false): array
    {
        // Force IPv4 type
        $ipData['iptype'] = 4;

        // Validate that at least one method of adding IPs is specified
        if (empty($ipData['ips']) && (empty($ipData['firstip']) || empty($ipData['lastip']))) {
            throw new VirtualizorApiException('Either specific IPs or an IP range must be provided');
        }

        // Validate MAC addresses if provided
        if (! empty($ipData['ips']) && ! empty($ipData['macs'])) {
            if (count($ipData['ips']) !== count($ipData['macs'])) {
                throw new VirtualizorApiException('Number of MAC addresses must match number of IPs');
            }
        }

        try {
            $response = $this->api->addIps($ipData);

            if ($raw) {
                return $response;
            }

            if (! empty($response['done'])) {
                return [
                    'success' => true,
                    'ips' => $response['done']['range'] ?? [],
                ];
            }

            throw new VirtualizorApiException('Failed to add IPv4 addresses: Unknown error');
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to add IPv4 addresses: '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Add IPv6 addresses
     *
     * @param array{
     *    ips6?: array,
     *    macs?: array,
     *    ipv6_1?: string,
     *    ipv6_2?: string,
     *    ipv6_3?: string,
     *    ipv6_4?: string,
     *    ipv6_5?: string,
     *    ipv6_6?: string,
     *    ipv6_num?: int,
     *    ippid?: int,
     *    ip_serid?: int
     * } $ipData IPv6 address data
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function addIpv6(array $ipData, bool $raw = false): array
    {
        // Force IPv6 type
        $ipData['iptype'] = 6;

        // Validate that at least one method of adding IPs is specified
        if (empty($ipData['ips6']) &&
            (empty($ipData['ipv6_1']) || empty($ipData['ipv6_2']) ||
             empty($ipData['ipv6_3']) || empty($ipData['ipv6_4']) ||
             empty($ipData['ipv6_5']) || empty($ipData['ipv6_6']) ||
             empty($ipData['ipv6_num']))) {
            throw new VirtualizorApiException(
                'Either specific IPv6 addresses or IPv6 segments with number of IPs must be provided'
            );
        }

        // Validate MAC addresses if provided
        if (! empty($ipData['ips6']) && ! empty($ipData['macs'])) {
            if (count($ipData['ips6']) !== count($ipData['macs'])) {
                throw new VirtualizorApiException('Number of MAC addresses must match number of IPv6 addresses');
            }
        }

        try {
            $response = $this->api->addIps($ipData);

            if ($raw) {
                return $response;
            }

            if (! empty($response['done'])) {
                return [
                    'success' => true,
                    'ips' => $response['done']['range'] ?? [],
                ];
            }

            throw new VirtualizorApiException('Failed to add IPv6 addresses: Unknown error');
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to add IPv6 addresses: '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Edit an IP address
     *
     * @param array{
     *    ip: string,
     *    mac_addr?: string,
     *    locked?: bool,
     *    note?: string
     * } $ipData IP update data
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function editIp(int $ipId, array $ipData, bool $raw = false): array
    {
        // Validate required fields
        if (! isset($ipData['ip'])) {
            throw new VirtualizorApiException('IP address is required');
        }

        // Convert boolean values
        if (isset($ipData['locked'])) {
            $ipData['locked'] = $ipData['locked'] ? 1 : 0;
        }

        try {
            $response = $this->api->editIp($ipId, $ipData);

            if ($raw) {
                return $response;
            }

            if (! empty($response['done'])) {
                return [
                    'success' => true,
                    'id' => $ipId,
                    'ip' => $response['ip'] ?? [],
                    'pools' => $response['ippools'] ?? [],
                ];
            }

            throw new VirtualizorApiException('Failed to update IP: Unknown error');
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to update IP {$ipId}: ".$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * List IPv6 subnets
     *
     * @param  int  $page  Page number
     * @param  int  $perPage  Records per page
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function listIpv6Subnets(int $page = 1, int $perPage = 50, bool $raw = false): array
    {
        try {
            $response = $this->api->listIpv6Subnets($page, $perPage);

            if ($raw) {
                return $response;
            }

            return array_map(function ($subnet) {
                return [
                    'id' => (int) $subnet['ipid'],
                    'pool_id' => (int) $subnet['ippid'],
                    'server_id' => (int) $subnet['ip_serid'],
                    'vps_id' => (int) $subnet['vpsid'],
                    'address' => $subnet['ip'],
                    'netmask' => $subnet['ipr_netmask'],
                    'is_primary' => (bool) $subnet['primary'],
                    'mac_address' => $subnet['mac_addr'],
                    'is_locked' => (bool) $subnet['locked'],
                    'note' => $subnet['note'],
                    'pool' => [
                        'name' => $subnet['ippool_name'],
                        'gateway' => $subnet['gateway'],
                        'netmask' => $subnet['netmask'],
                        'nameservers' => [
                            'ns1' => $subnet['ns1'],
                            'ns2' => $subnet['ns2'],
                        ],
                        'nat' => [
                            'enabled' => (bool) $subnet['nat'],
                            'name' => $subnet['nat_name'],
                        ],
                        'routing' => (bool) $subnet['routing'],
                        'internal' => (bool) $subnet['internal'],
                        'bridge' => $subnet['bridge'],
                        'mtu' => (int) $subnet['mtu'],
                        'vlan' => (int) $subnet['vlan'],
                    ],
                    'hostname' => $subnet['hostname'],
                ];
            }, $response['ips'] ?? []);
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to list IPv6 subnets: '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Search IPv6 subnets
     *
     * @param array{
     *    ipsearch?: string,
     *    ippoolsearch?: string,
     *    ippid?: int,
     *    lockedsearch?: string
     * } $filters Search filters
     * @param  int  $page  Page number
     * @param  int  $perPage  Records per page
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function searchIpv6Subnets(array $filters = [], int $page = 1, int $perPage = 50, bool $raw = false): array
    {
        try {
            $response = $this->api->searchIpv6Subnets($filters, $page, $perPage);

            if ($raw) {
                return $response;
            }

            return array_map(function ($subnet) {
                return [
                    'id' => (int) $subnet['ipid'],
                    'pool_id' => (int) $subnet['ippid'],
                    'server_id' => (int) $subnet['ip_serid'],
                    'vps_id' => (int) $subnet['vpsid'],
                    'address' => $subnet['ip'],
                    'netmask' => $subnet['ipr_netmask'],
                    'is_primary' => (bool) $subnet['primary'],
                    'mac_address' => $subnet['mac_addr'],
                    'is_locked' => (bool) $subnet['locked'],
                    'note' => $subnet['note'] ?? null,
                    'pool' => [
                        'name' => $subnet['ippool_name'],
                        'gateway' => $subnet['gateway'],
                        'netmask' => $subnet['netmask'],
                        'nameservers' => [
                            'ns1' => $subnet['ns1'],
                            'ns2' => $subnet['ns2'],
                        ],
                        'nat' => [
                            'enabled' => (bool) $subnet['nat'],
                            'name' => $subnet['nat_name'],
                        ],
                        'routing' => (bool) $subnet['routing'],
                        'internal' => (bool) $subnet['internal'],
                        'bridge' => $subnet['bridge'],
                        'mtu' => (int) $subnet['mtu'],
                        'vlan' => (int) $subnet['vlan'],
                    ],
                    'hostname' => $subnet['hostname'],
                ];
            }, $response['ips'] ?? []);
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to search IPv6 subnets: '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Edit IPv6 subnet
     *
     * @param array{
     *    ip: string,
     *    netmask?: int,
     *    locked?: bool
     * } $subnetData Subnet update data
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function editIpv6Subnet(int $subnetId, array $subnetData, bool $raw = false): array
    {
        // Validate required fields
        if (! isset($subnetData['ip'])) {
            throw new VirtualizorApiException('IP address is required');
        }

        // Validate netmask if provided
        if (isset($subnetData['netmask']) && ! in_array($subnetData['netmask'], [64, 80, 96, 112])) {
            throw new VirtualizorApiException('Invalid netmask value. Allowed values are: 64, 80, 96, 112');
        }

        // Convert boolean values
        if (isset($subnetData['locked'])) {
            $subnetData['locked'] = $subnetData['locked'] ? 1 : 0;
        }

        try {
            $response = $this->api->editIpv6Subnet($subnetId, $subnetData);

            if ($raw) {
                return $response;
            }

            return [
                'success' => true,
                'id' => $subnetId,
                'ip' => $subnetData['ip'],
                'netmask' => $subnetData['netmask'] ?? null,
                'locked' => $subnetData['locked'] ?? null,
                'timestamp' => $response['timenow'] ?? null,
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to update IPv6 subnet {$subnetId}: ".$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Delete one or more IPv6 subnets
     *
     * @param  int|array  $subnetIds  Single subnet ID or array of subnet IDs
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function deleteIpv6Subnet($subnetIds, bool $raw = false): array
    {
        try {
            $response = $this->api->deleteIpv6Subnets($subnetIds);

            if ($raw) {
                return $response;
            }

            if (! empty($response['done'])) {
                return [
                    'success' => true,
                    'remaining_subnets' => array_map(function ($subnet) {
                        return [
                            'id' => (int) $subnet['ipid'],
                            'pool_id' => (int) $subnet['ippid'],
                            'server_id' => (int) $subnet['ip_serid'],
                            'vps_id' => (int) $subnet['vpsid'],
                            'address' => $subnet['ip'],
                            'netmask' => $subnet['ipr_netmask'],
                            'is_primary' => (bool) $subnet['primary'],
                            'mac_address' => $subnet['mac_addr'],
                            'is_locked' => (bool) $subnet['locked'],
                            'pool' => [
                                'name' => $subnet['ippool_name'],
                                'gateway' => $subnet['gateway'],
                                'netmask' => $subnet['netmask'],
                                'nameservers' => [
                                    'ns1' => $subnet['ns1'],
                                    'ns2' => $subnet['ns2'],
                                ],
                                'nat' => [
                                    'enabled' => (bool) $subnet['nat'],
                                    'name' => $subnet['nat_name'],
                                ],
                                'routing' => (bool) $subnet['routing'],
                                'internal' => (bool) $subnet['internal'],
                                'bridge' => $subnet['bridge'],
                                'mtu' => (int) $subnet['mtu'],
                                'vlan' => (int) $subnet['vlan'],
                            ],
                            'hostname' => $subnet['hostname'],
                        ];
                    }, $response['ips'] ?? []),
                ];
            }

            throw new VirtualizorApiException('Failed to delete IPv6 subnet(s): Unknown error');
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to delete IPv6 subnet(s): '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Add IPv6 subnet
     *
     * @param array{
     *    netmask: int,
     *    input_netmask: int,
     *    ips6?: array,
     *    ipv6_1?: string,
     *    ipv6_2?: string,
     *    ipv6_3?: string,
     *    ipv6_4?: string,
     *    ipv6_5?: string,
     *    ipv6_6?: string,
     *    ipv6_7?: string,
     *    ipv6_num: int,
     *    ippid?: int
     * } $subnetData Subnet creation data
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function addIpv6Subnet(array $subnetData, bool $raw = false): array
    {
        // Validate required fields
        if (! isset($subnetData['netmask'])) {
            throw new VirtualizorApiException('Netmask is required');
        }
        if (! isset($subnetData['input_netmask'])) {
            throw new VirtualizorApiException('Input netmask is required');
        }
        if (! isset($subnetData['ipv6_num'])) {
            throw new VirtualizorApiException('Number of IPv6 subnets is required');
        }

        // Validate netmask values
        if (! in_array($subnetData['netmask'], [48, 64, 80, 96, 112])) {
            throw new VirtualizorApiException('Invalid netmask value. Allowed values are: 48, 64, 80, 96, 112');
        }

        // Validate input netmask range
        if ($subnetData['input_netmask'] < 32 || $subnetData['input_netmask'] > 108 || $subnetData['input_netmask'] % 4 !== 0) {
            throw new VirtualizorApiException('Invalid input netmask value. Must be between 32 and 108 with increments of 4');
        }

        // Validate required IPv6 segments based on input_netmask
        $this->validateIpv6Segments($subnetData);

        try {
            $response = $this->api->addIpv6Subnet($subnetData);

            if ($raw) {
                return $response;
            }

            if (! empty($response['done'])) {
                return [
                    'success' => true,
                    'subnets' => $response['done']['range'] ?? [],
                ];
            }

            throw new VirtualizorApiException('Failed to add IPv6 subnet: Unknown error');
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to add IPv6 subnet: '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Validate required IPv6 segments based on input netmask
     *
     * @param  array  $data  Subnet data
     *
     * @throws VirtualizorApiException
     */
    private function validateIpv6Segments(array $data): void
    {
        $inputNetmask = $data['input_netmask'];

        // First two segments are always required
        if (empty($data['ipv6_1']) || empty($data['ipv6_2'])) {
            throw new VirtualizorApiException('First two IPv6 segments are required');
        }

        // Additional segment requirements based on input_netmask
        if ($inputNetmask >= 36 && empty($data['ipv6_3'])) {
            throw new VirtualizorApiException('Third IPv6 segment is required for input_netmask >= 36');
        }
        if ($inputNetmask >= 52 && empty($data['ipv6_4'])) {
            throw new VirtualizorApiException('Fourth IPv6 segment is required for input_netmask >= 52');
        }
        if ($inputNetmask >= 68 && empty($data['ipv6_5'])) {
            throw new VirtualizorApiException('Fifth IPv6 segment is required for input_netmask >= 68');
        }
        if ($inputNetmask >= 84 && empty($data['ipv6_6'])) {
            throw new VirtualizorApiException('Sixth IPv6 segment is required for input_netmask >= 84');
        }
        if ($inputNetmask >= 100 && empty($data['ipv6_7'])) {
            throw new VirtualizorApiException('Seventh IPv6 segment is required for input_netmask >= 100');
        }
    }
}
