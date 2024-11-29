<?php

namespace CODEIQ\Virtualizor\Api;

class AdminApi extends BaseApi
{
    /**
     * List all users
     *
     * @param int $page Page number
     * @param int $perPage Number of records per page
     * @param array $post Additional filters
     */
    public function users(int $page = 1, int $perPage = 50, array $post = []): array
    {
        $path = 'index.php?act=users';

        return $this->makeRequest($path, [
            'page' => $page,
            'reslen' => $perPage,
            ...$post,
        ]);
    }

    public function addVs(array $params)
    {
        return $this->makeRequest('index.php?act=addvs', [
            'addvps' => 1,
            ...$params,
        ], 'POST');
    }

    public function deleteVs(int $vpsId)
    {
        return $this->makeRequest('index.php?act=vs', [
            'delete' => $vpsId,
        ]);
    }

    /**
     * Create a new user
     *
     * @param  array  $params  User creation parameters
     */
    public function adduser(array $params): array
    {
        return $this->makeRequest('index.php?act=adduser', $params, 'POST');
    }

    /**
     * Edit a user
     *
     * @param  int  $userId  User ID to edit
     * @param  array  $params  User update parameters
     */
    public function edituser(int $userId, array $params): array
    {
        return $this->makeRequest("index.php?act=edituser&uid={$userId}", [
            'edituser' => 1,
            ...$params,
        ], 'POST');
    }

    /**
     * Delete user(s)
     *
     * @param  int|int[]  $userIds  Single user ID or array of user IDs
     * @param  bool  $deleteVms  Whether to delete associated VMs
     */
    public function deleteUsers(int|array $userIds, bool $deleteVms = false): array
    {
        $params = [
            'delete' => is_array($userIds) ? implode(',', $userIds) : (string) $userIds,
        ];

        if (! $deleteVms) {
            $params['dont_delete_vms'] = 1;
        }

        return $this->makeRequest('index.php?act=users', $params, 'POST');
    }

    /**
     * Suspend user(s)
     *
     * @param  int|int[]  $userIds  Single user ID or array of user IDs
     */
    public function suspendUsers(int|array $userIds): array
    {
        $params = [
            'suspend' => is_array($userIds) ? implode(',', $userIds) : (string) $userIds,
        ];

        return $this->makeRequest('index.php?act=users', $params, 'POST');
    }

    /**
     * Unsuspend user(s)
     *
     * @param  int|int[]  $userIds  Single user ID or array of user IDs
     */
    public function unsuspendUsers(int|array $userIds): array
    {
        $params = [
            'unsuspend' => is_array($userIds) ? implode(',', $userIds) : (string) $userIds,
        ];

        return $this->makeRequest('index.php?act=users', $params, 'POST');
    }

    /**
     * Create Administrator ACL
     *
     * @param  array  $params  ACL parameters
     */
    public function addAdminAcl(array $params): array
    {
        return $this->makeRequest('index.php?act=add_admin_acl', [
            'add_admin_acl' => 1,
            ...$params,
        ], 'POST');
    }

    /**
     * List Administrator ACLs
     *
     * @param  int  $page  Page number
     * @param  int  $reslen  Number of records per page
     */
    public function listAcls(int $page = 1, int $reslen = 50): array
    {
        return $this->makeRequest('index.php?act=admin_acl', [
            'page' => $page,
            'reslen' => $reslen,
        ]);
    }

    /**
     * Edit Administrator ACL
     *
     * @param  int  $aclId  ACL ID to edit
     * @param  array  $params  ACL parameters
     */
    public function editAdminAcl(int $aclId, array $params): array
    {
        return $this->makeRequest("index.php?act=edit_admin_acl&aclid={$aclId}", [
            'edit_admin_acl' => 1,
            ...$params,
        ], 'POST');
    }

    /**
     * Delete Administrator ACL(s)
     *
     * @param  int|string|array  $aclIds  Single ACL ID, comma-separated IDs, or array of IDs
     */
    public function deleteAcls($aclIds): array
    {
        $params = [
            'delete' => is_array($aclIds) ? implode(',', $aclIds) : $aclIds,
        ];

        return $this->makeRequest('index.php?act=admin_acl', $params, 'POST');
    }

    /**
     * List all servers
     *
     * @param  int  $page  Page number
     * @param  int  $reslen  Number of records per page
     * @param  array  $filters  Optional filters (servername, serverip, ptype)
     */
    public function listServers(int $page = 1, int $reslen = 50, array $filters = []): array
    {
        return $this->makeRequest('index.php?act=servers', [
            'page' => $page,
            'reslen' => $reslen,
            ...$filters,
        ]);
    }

    /**
     * Add a new server
     *
     * @param  array  $params  Server parameters
     */
    public function addServer(array $params): array
    {
        return $this->makeRequest('index.php?act=addserver', $params, 'POST');
    }

    /**
     * Edit server settings
     *
     * @param  int  $serverId  Server ID to edit
     * @param  array  $params  Server parameters
     */
    public function editServer(int $serverId, array $params): array
    {
        return $this->makeRequest("index.php?act=editserver&serid={$serverId}", $params, 'POST');
    }

    /**
     * Delete a server
     *
     * @param  int  $serverId  Server ID to delete
     */
    public function deleteServer(int $serverId): array
    {
        return $this->makeRequest('index.php?act=servers', [
            'delete' => $serverId,
        ], 'POST');
    }

    /**
     * Get server loads information
     */
    public function getServerLoads(): array
    {
        return $this->makeRequest('index.php?act=serverloads');
    }

    /**
     * List server groups
     *
     * @param  array  $filters  Optional filters (sg_name)
     */
    public function listServerGroups(array $filters = []): array
    {
        return $this->makeRequest('index.php?act=servergroups', $filters);
    }

    /**
     * Create a new server group
     *
     * @param  array  $params  Server group parameters
     */
    public function addServerGroup(array $params): array
    {
        return $this->makeRequest('index.php?act=addsg', [
            'addsg' => 1,
            ...$params,
        ], 'POST');
    }

    /**
     * Edit a server group
     *
     * @param  int  $groupId  Server group ID to edit
     * @param  array  $params  Server group parameters
     */
    public function editServerGroup(int $groupId, array $params): array
    {
        return $this->makeRequest("index.php?act=editsg&sgid={$groupId}", [
            'editsg' => 1,
            ...$params,
        ], 'POST');
    }

    /**
     * Delete server group(s)
     *
     * @param  int|string|array  $groupIds  Single group ID, comma-separated IDs, or array of IDs
     */
    public function deleteServerGroups($groupIds): array
    {
        return $this->makeRequest('index.php?act=servergroups', [
            'delete' => is_array($groupIds) ? implode(',', $groupIds) : $groupIds,
        ], 'POST');
    }

    /**
     * List backup servers
     *
     * @param  int  $page  Page number
     * @param  int  $reslen  Number of records per page
     * @param  array  $filters  Optional filters (name, hostname, type)
     */
    public function listBackupServers(int $page = 1, int $reslen = 50, array $filters = []): array
    {
        return $this->makeRequest('index.php?act=backupservers', [
            'page' => $page,
            'reslen' => $reslen,
            ...$filters,
        ]);
    }

    /**
     * Add a new backup server
     *
     * @param  array  $params  Backup server parameters
     */
    public function addBackupServer(array $params): array
    {
        return $this->makeRequest('index.php?act=addbackupservers', $params, 'POST');
    }

    /**
     * Edit a backup server
     *
     * @param  int  $serverId  Backup server ID to edit
     * @param  array  $params  Backup server parameters
     */
    public function editBackupServer(int $serverId, array $params): array
    {
        return $this->makeRequest("index.php?act=editbackupserver&id={$serverId}", $params, 'POST');
    }

    /**
     * Delete backup server(s)
     *
     * @param  int|string|array  $serverIds  Single server ID, comma-separated IDs, or array of IDs
     */
    public function deleteBackupServers($serverIds): array
    {
        return $this->makeRequest('index.php?act=backupservers', [
            'delete' => is_array($serverIds) ? implode(',', $serverIds) : $serverIds,
        ], 'POST');
    }

    /**
     * Test backup server connectivity
     *
     * @param  int  $serverId  Backup server ID to test
     */
    public function testBackupServer(int $serverId): array
    {
        return $this->makeRequest('index.php?act=backupservers', [
            'test' => $serverId,
        ]);
    }

    /**
     * List IP pools
     *
     * @param  int  $page  Page number
     * @param  int  $reslen  Number of records per page
     */
    public function listIpPools(int $page = 1, int $reslen = 50): array
    {
        return $this->makeRequest('index.php?act=ippool', [
            'page' => $page,
            'reslen' => $reslen,
        ]);
    }

    /**
     * Create a new IP pool
     *
     * @param  array  $params  IP pool parameters
     */
    public function addIpPool(array $params): array
    {
        return $this->makeRequest('index.php?act=addippool', $params, 'POST');
    }

    /**
     * Edit an IP pool
     *
     * @param  int  $poolId  IP pool ID to edit
     * @param  array  $params  IP pool parameters
     */
    public function editIpPool(int $poolId, array $params): array
    {
        return $this->makeRequest('index.php?act=editippool', [
            'ippid' => $poolId,
            'editippool' => 1,
            ...$params,
        ], 'POST');
    }

    /**
     * Delete IP pool(s)
     *
     * @param  int|string|array  $poolIds  Single pool ID, comma-separated IDs, or array of IDs
     */
    public function deleteIpPools($poolIds): array
    {
        return $this->makeRequest('index.php?act=ippool', [
            'delete' => is_array($poolIds) ? implode(',', $poolIds) : $poolIds,
        ], 'POST');
    }

    /**
     * List IP addresses
     *
     * @param  int  $page  Page number
     * @param  int  $reslen  Number of records per page
     */
    public function listIps(int $page = 1, int $reslen = 50): array
    {
        return $this->makeRequest('index.php?act=ips', [
            'page' => $page,
            'reslen' => $reslen,
        ]);
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
     * @param  int  $reslen  Number of records per page
     */
    public function searchIpPools(array $filters = [], int $page = 1, int $reslen = 50): array
    {
        return $this->makeRequest('index.php?act=ippool', [
            'page' => $page,
            'reslen' => $reslen,
            ...$filters,
        ]);
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
     * @param  int  $reslen  Number of records per page
     */
    public function searchIps(array $filters = [], int $page = 1, int $reslen = 50): array
    {
        return $this->makeRequest('index.php?act=ips', [
            'page' => $page,
            'reslen' => $reslen,
            ...$filters,
        ]);
    }

    /**
     * Add IP addresses
     *
     * @param array{
     *    iptype: int,
     *    ips?: array,
     *    macs?: array,
     *    firstip?: string,
     *    lastip?: string,
     *    ippid?: int,
     *    ip_serid?: int
     * } $params IP parameters
     */
    public function addIps(array $params): array
    {
        return $this->makeRequest('index.php?act=addips', $params, 'POST');
    }

    /**
     * Edit an IP address
     *
     * @param  int  $ipId  IP ID to edit
     * @param  array  $params  IP parameters
     */
    public function editIp(int $ipId, array $params): array
    {
        return $this->makeRequest('index.php?act=editips', [
            'ipid' => $ipId,
            'editip' => 1,
            ...$params,
        ], 'POST');
    }

    /**
     * List IPv6 subnets
     *
     * @param  int  $page  Page number
     * @param  int  $reslen  Number of records per page
     */
    public function listIpv6Subnets(int $page = 1, int $reslen = 50): array
    {
        return $this->makeRequest('index.php?act=ipranges', [
            'page' => $page,
            'reslen' => $reslen,
        ]);
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
     * @param  int  $reslen  Number of records per page
     */
    public function searchIpv6Subnets(array $filters = [], int $page = 1, int $reslen = 50): array
    {
        return $this->makeRequest('index.php?act=ipranges', [
            'page' => $page,
            'reslen' => $reslen,
            ...$filters,
        ]);
    }

    /**
     * Edit IPv6 subnet
     *
     * @param  int  $subnetId  Subnet ID to edit
     * @param  array  $params  Subnet parameters
     */
    public function editIpv6Subnet(int $subnetId, array $params): array
    {
        return $this->makeRequest('index.php?act=editiprange', [
            'ipid' => $subnetId,
            'editip' => 1,
            ...$params,
        ], 'POST');
    }

    /**
     * Delete IPv6 subnet(s)
     *
     * @param  int|string|array  $subnetIds  Single subnet ID, comma-separated IDs, or array of IDs
     */
    public function deleteIpv6Subnets($subnetIds): array
    {
        return $this->makeRequest('index.php?act=ipranges', [
            'delete' => is_array($subnetIds) ? implode(',', $subnetIds) : $subnetIds,
        ], 'POST');
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
     * } $params Subnet parameters
     */
    public function addIpv6Subnet(array $params): array
    {
        return $this->makeRequest('index.php?act=addiprange', [
            'submitip' => 1,
            ...$params,
        ], 'POST');
    }

    /**
     * List virtual servers
     *
     * @param  array  $filters  Search filters
     * @param  int  $page  Page number
     * @param  int  $perPage  Records per page
     * @param array{
     *    vpsid?: int,
     *    vpsname?: string,
     *    vpsip?: string,
     *    vpshostname?: string,
     *    vsstatus?: string,
     *    vstype?: string,
     *    speedcap?: string,
     *    user?: string,
     *    vsgid?: string|array,
     *    vserid?: string|array,
     *    plid?: string|array,
     *    bpid?: string|array,
     *    search?: string
     * } $filters Search filters
     * @param  int  $page  Page number
     * @param  int  $reslen  Number of records per page
     */
    public function listVirtualServers(array $filters = [], int $page = 1, int $perPage = 50): array
    {
        return $this->makeRequest('index.php?act=vs', [
            'page' => $page,
            'reslen' => $perPage,
            ...$filters,
        ]);
    }

    /**
     * Create a new virtual server
     *
     * @param array{
     *    virt: string,
     *    node_select?: int,
     *    user_email: string,
     *    user_pass: string,
     *    hostname: string,
     *    rootpass: string,
     *    slave_server?: int,
     *    server_group?: int,
     *    osid: int,
     *    ips: array,
     *    space: array|int,
     *    ram: int,
     *    bandwidth: int,
     *    cores: int,
     *    bus_driver?: string,
     *    bus_driver_num?: int,
     *    speed_cap_up?: int,
     *    speed_cap_down?: int,
     *    plid?: int,
     *    network_speed?: int,
     *    mgs?: array,
     *    priority?: int,
     *    cpu?: int,
     *    burst?: int,
     *    cpu_percent?: int,
     *    iso?: string,
     *    stid?: int,
     *    vnc?: int,
     *    vncpass?: string,
     *    swapram?: int,
     *    shadow?: int,
     *    hvm?: int,
     *    boot?: string,
     *    num_ips?: int,
     *    num_ips6?: int,
     *    num_ips6_subnet?: int,
     *    noemail?: int,
     *    fname?: string,
     *    lname?: string,
     *    uid?: int,
     *    dnsplid?: int,
     *    band_suspend?: int,
     *    tuntap?: int,
     *    ppp?: int,
     *    fuse?: int,
     *    ipip?: int,
     *    ipgre?: int,
     *    nfs?: int,
     *    quotaugidlimit?: int,
     *    iolimit?: int,
     *    iopslimit?: int,
     *    vif_type?: string,
     *    nic_type?: string,
     *    ips_int?: array,
     *    virtio?: int,
     *    kvm_cache?: string,
     *    upload_speed?: int,
     *    dns?: array,
     *    control_panel?: string,
     *    recipe?: int,
     *    cpupin?: array,
     *    cpu_mode?: string,
     *    topology_sockets?: int,
     *    topology_cores?: int,
     *    topology_threads?: int,
     *    io_mode?: string,
     *    kvm_vga?: int,
     *    acceleration?: int,
     *    disable_ebtables?: int,
     *    rdp?: int,
     *    vnc_keymap?: string,
     *    osreinstall_limit?: int,
     *    admin_managed?: int,
     *    disable_nw_config?: int,
     *    total_iops_sec?: int,
     *    read_bytes_sec?: int,
     *    write_bytes_sec?: int,
     *    ha?: int,
     *    os_type?: string,
     *    rtc?: int,
     *    unprivileged?: int,
     *    ssd_emulation?: int,
     *    disable_password?: int,
     *    discard?: int,
     *    ssh_options?: string,
     *    sshkey?: string,
     *    private_key?: string,
     *    bios?: string,
     *    enable_tpm?: int,
     *    bootord?: string,
     *    disable_guest_agent?: int,
     *    demo?: int,
     *    demo_date?: string,
     *    demo_time?: string,
     *    demo_action?: int,
     *    enable_ver_scaling?: int,
     *    ver_max_ram?: int,
     *    ver_ram_threshold?: int,
     *    ver_ram_inc_by?: int,
     *    ver_max_cpu?: int,
     *    ver_cpu_threshold?: int,
     *    ver_cpu_inc_by?: int
     * } $params VPS creation parameters
     */
    public function createVps(array $params): array
    {
        return $this->makeRequest('index.php?act=addvs', [
            'addvps' => 1,
            ...$params,
        ], 'POST');
    }

    /**
     * Manage a virtual server
     *
     * @param  int  $vpsId  VPS ID to manage
     * @param  array  $params  Management parameters
     */
    public function manageVps(int $vpsId, array $params): array
    {
        return $this->makeRequest("index.php?act=managevps&vpsid={$vpsId}", [
            'theme_edit' => 1,
            'editvps' => 1,
            ...$params,
        ], 'POST');
    }

    /**
     * Start a virtual server
     *
     * @param  int  $vpsId  VPS ID to start
     */
    public function startVps(int $vpsId): array
    {
        return $this->makeRequest("index.php?act=vs&action=start&vpsid={$vpsId}");
    }

    /**
     * Stop a virtual server
     *
     * @param  int  $vpsId  VPS ID to stop
     */
    public function stopVps(int $vpsId): array
    {
        return $this->makeRequest("index.php?act=vs&action=stop&vpsid={$vpsId}");
    }

    /**
     * Restart a virtual server
     *
     * @param  int  $vpsId  VPS ID to restart
     */
    public function restartVps(int $vpsId): array
    {
        return $this->makeRequest("index.php?act=vs&action=restart&vpsid={$vpsId}");
    }

    /**
     * Power off a virtual server
     *
     * @param  int  $vpsId  VPS ID to power off
     */
    public function poweroffVps(int $vpsId): array
    {
        return $this->makeRequest("index.php?act=vs&action=poweroff&vpsid={$vpsId}");
    }

    /**
     * Suspend a virtual server
     *
     * @param  int  $vpsId  VPS ID to suspend
     * @param  array  $params  Additional parameters (suspend_reason)
     */
    public function suspendVps(int $vpsId, array $params = []): array
    {
        return $this->makeRequest("index.php?act=vs&suspend={$vpsId}", $params);
    }

    /**
     * Unsuspend a virtual server
     *
     * @param  int  $vpsId  VPS ID to unsuspend
     */
    public function unsuspendVps(int $vpsId): array
    {
        return $this->makeRequest("index.php?act=vs&unsuspend={$vpsId}");
    }

    /**
     * Suspend network for a virtual server
     *
     * @param  int  $vpsId  VPS ID to suspend network
     */
    public function suspendNetworkVps(int $vpsId): array
    {
        return $this->makeRequest("index.php?act=vs&suspend_net={$vpsId}");
    }

    /**
     * Unsuspend network for a virtual server
     *
     * @param  int  $vpsId  VPS ID to unsuspend network
     */
    public function unsuspendNetworkVps(int $vpsId): array
    {
        return $this->makeRequest("index.php?act=vs&unsuspend_net={$vpsId}");
    }

    /**
     * Rebuild a virtual server
     *
     * @param array{
     *    vpsid: int,
     *    osid: int,
     *    newpass: string,
     *    conf: string,
     *    format_primary?: int,
     *    eu_send_rebuild_email?: int,
     *    recipe?: int,
     *    sshkey?: string
     * } $params Rebuild parameters
     */
    public function rebuildVps(array $params): array
    {
        return $this->makeRequest('index.php?act=rebuild', [
            'reos' => 1,
            ...$params,
        ], 'POST');
    }

    /**
     * Migrate virtual server(s)
     *
     * @param array{
     *    vpsid: array,
     *    storage: array,
     *    from_server?: int,
     *    from_ip: string,
     *    from_pass: string,
     *    speed_limit?: int,
     *    to_server?: int,
     *    preserve_ip?: int,
     *    del_orig?: int,
     *    ignore_if_vdfconflict?: int,
     *    disable_gzip?: int,
     *    live_mig?: int
     * } $params Migration parameters
     */
    public function migrateVps(array $params): array
    {
        return $this->makeRequest('index.php?act=migrate', [
            'migrate' => 1,
            'migrate_but' => 1,
            ...$params,
        ], 'POST');
    }

    /**
     * Clone virtual server(s)
     *
     * @param array{
     *    vpsid: array,
     *    from_server: int|string,
     *    storage: array,
     *    to_server: int,
     *    speed_limit?: int,
     *    ignore_if_vdfconflict?: int,
     *    disable_gzip?: int,
     *    preserve_ip?: int
     * } $params Clone parameters
     */
    public function cloneVps(array $params): array
    {
        return $this->makeRequest('index.php?act=clone', $params, 'POST');
    }

    /**
     * Get VNC information for a virtual server
     *
     * @param  int  $vpsId  VPS ID to get VNC info for
     */
    public function getVncInfo(int $vpsId): array
    {
        return $this->makeRequest('index.php?act=vnc', [
            'novnc' => $vpsId,
        ], 'POST');
    }

    /**
     * Add domain forwarding record for a VPS
     *
     * @param array{
     *    serid: int,
     *    vpsuuid: string,
     *    protocol: string,
     *    src_hostname: string,
     *    src_port: int|string,
     *    dest_ip: string,
     *    dest_port: int|string,
     *    action: string
     * } $params Domain forwarding parameters
     */
    public function addDomainForwarding(array $params): array
    {
        return $this->makeRequest('index.php?act=haproxy', [
            'action' => 'addvdf',
            ...$params,
        ], 'POST');
    }

    /**
     * Edit domain forwarding record
     *
     * @param int $recordId Record ID to edit
     * @param array{
     *    serid: int,
     *    vpsuuid: string,
     *    protocol: string,
     *    src_hostname: string,
     *    src_port: int|string,
     *    dest_ip: string,
     *    dest_port: int|string
     * } $params Domain forwarding parameters
     * @return array
     */
    public function editDomainForwarding(int $recordId, array $params): array
    {
        return $this->makeRequest('index.php?act=haproxy', [
            'id' => $recordId,
            'action' => 'editvdf',
            ...$params
        ], 'POST');
    }

    /**
     * Delete domain forwarding record(s)
     *
     * @param int|string|array $recordIds Single record ID, comma-separated IDs, or array of IDs
     * @return array
     */
    public function deleteDomainForwarding($recordIds): array
    {
        return $this->makeRequest('index.php?act=haproxy', [
            'action' => 'delvdf',
            'ids' => is_array($recordIds) ? implode(',', $recordIds) : $recordIds
        ], 'POST');
    }

    /**
     * List domain forwarding records
     *
     * @param array{
     *    s_id?: int,
     *    s_serid?: int,
     *    s_vpsid?: int,
     *    s_protocol?: string,
     *    s_src_hostname?: string,
     *    s_src_port?: int|string,
     *    s_dest_ip?: string,
     *    s_dest_port?: int|string
     * } $filters Search filters
     * @param int $page Page number
     * @param int $perPage Records per page
     * @return array
     */
    public function listDomainForwarding(array $filters = [], int $page = 1, int $perPage = 50): array
    {
        return $this->makeRequest('index.php?act=haproxy', [
            'page' => $page,
            'reslen' => $perPage,
            'haproxysearch' => 1,
            ...$filters
        ], 'POST');
    }

    /**
     * Get High Availability status
     *
     * @param int|null $serverGroupId Optional server group ID for specific HA cluster
     * @return array
     */
    public function getHaStatus(?int $serverGroupId = null): array
    {
        $params = [];
        if ($serverGroupId !== null) {
            $params['get_ha_stats'] = $serverGroupId;
        }
        
        return $this->makeRequest('index.php?act=ha', $params);
    }

    /**
     * Reset bandwidth for a virtual server
     *
     * @param int $vpsId VPS ID to reset bandwidth
     * @return array
     */
    public function resetBandwidth(int $vpsId): array
    {
        return $this->makeRequest("index.php?act=vs&bwreset={$vpsId}");
    }

    /**
     * Get status for virtual server(s)
     *
     * @param array|int $vpsIds Single VPS ID or array of VPS IDs
     * @return array
     */
    public function getVpsStatus(array|int $vpsIds): array
    {
        $ids = is_array($vpsIds) ? $vpsIds : [$vpsIds];
        return $this->makeRequest("index.php?act=vs&vs_status=" . implode(',', $ids));
    }

    /**
     * Update network rules for a virtual server
     *
     * @param int $vpsId VPS ID to update network rules
     * @return array
     */
    public function updateVpsNetworkRules(int $vpsId): array
    {
        return $this->makeRequest("index.php?act=vs&action=vs_netrestrict&vpsid={$vpsId}");
    }

    /**
     * Import from SolusVM
     *
     * @param array{
     *    ta: string,
     *    changeserid?: int,
     *    solusvm_ng?: int,
     *    solusvm_plans?: int,
     *    solusvm_users?: int,
     *    solusvm_ips?: int,
     *    solusvm_os?: int,
     *    solusvm_vps?: int
     * } $params Import parameters
     * @return array
     */
    public function importSolusvm(array $params): array
    {
        return $this->makeRequest('index.php?act=import&sa=solusvm', $params);
    }

    /**
     * Import from Proxmox
     *
     * @param array{
     *    ta: string,
     *    changeserid?: int,
     *    proxmox_users?: int,
     *    proxmox_storages?: int,
     *    proxmox_vps?: int
     * } $params Import parameters
     * @return array
     */
    public function importProxmox(array $params): array
    {
        return $this->makeRequest('index.php?act=import&sa=proxmox', $params);
    }

    /**
     * Import from Feathur
     *
     * @param array{
     *    ta: string,
     *    changeserid?: int,
     *    feathur_users?: int,
     *    feathur_ips?: int,
     *    feathur_os?: int,
     *    feathur_vps?: int
     * } $params Import parameters
     * @return array
     */
    public function importFeathur(array $params): array
    {
        return $this->makeRequest('index.php?act=import&sa=feathur', $params);
    }

    /**
     * Import from HyperVM
     *
     * @param array{
     *    ta: string,
     *    changeserid?: int,
     *    hypervm_plans?: int,
     *    hypervm_users?: int,
     *    hypervm_ips?: int,
     *    hypervm_os?: int,
     *    hypervm_vps?: int
     * } $params Import parameters
     * @return array
     */
    public function importHypervm(array $params): array
    {
        return $this->makeRequest('index.php?act=import&sa=hypervm', $params);
    }

    /**
     * Import from OpenVZ
     *
     * @param array{
     *    changeserid: int,
     *    importvps?: int,
     *    vsbw_*?: int,
     *    vsuser_*?: int
     * } $params Import parameters
     * @return array
     */
    public function importOpenvz(array $params): array
    {
        return $this->makeRequest('index.php?act=import&sa=openvz', $params);
    }

    /**
     * Import from XEN Server
     *
     * @param array{
     *    changeserid: int,
     *    importvps?: int,
     *    vsbw_*?: int,
     *    vsuser_*?: int
     * } $params Import parameters
     * @return array
     */
    public function importXenServer(array $params): array
    {
        return $this->makeRequest('index.php?act=import&sa=xcp', $params);
    }

    /**
     * Import from OpenVZ 7
     *
     * @param array{
     *    changeserid: int,
     *    importvps?: int,
     *    vsbw_*?: int,
     *    vsuser_*?: int
     * } $params Import parameters
     * @return array
     */
    public function importOpenvz7(array $params): array
    {
        return $this->makeRequest('index.php?act=import&sa=openvz7', $params);
    }

    /**
     * List SSH keys
     *
     * @param int $userId User ID to list SSH keys for
     * @return array
     */
    public function listSshKeys(int $userId): array
    {
        return $this->makeRequest('index.php?act=users', [
            'list_ssh_keys' => 1,
            'uid' => $userId
        ]);
    }

    // Add other admin API methods here
}
