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
     * List SSH keys for a user
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

    /**
     * Add SSH keys to a VPS
     *
     * @param int $vpsId VPS ID to add SSH keys to
     * @param array<int> $sshKeyIds Array of SSH key IDs to add
     * @return array
     */
    public function addSshKeys(int $vpsId, array $sshKeyIds): array
    {
        return $this->makeRequest("index.php?act=managevps&vpsid={$vpsId}&add_ssh_keys=1", [
            'sshkeys' => $sshKeyIds
        ], 'POST');
    }

    /**
     * Lock a VPS
     *
     * @param int $vpsId VPS ID to lock
     * @param string $reason Reason for locking (optional)
     * @return array
     */
    public function lockVps(int $vpsId, string $reason = ''): array
    {
        return $this->makeRequest("index.php?act=vs&action=lock&vpsid={$vpsId}", [
            'reason' => $reason
        ], 'POST');
    }

    /**
     * Unlock a VPS
     *
     * @param int $vpsId VPS ID to unlock
     * @return array
     */
    public function unlockVps(int $vpsId): array
    {
        return $this->makeRequest("index.php?act=vs&action=unlock&vpsid={$vpsId}");
    }

    /**
     * List storage information
     *
     * @param array{
     *    name?: string,
     *    path?: string
     * } $filters Search filters
     * @param int $page Page number
     * @param int $perPage Records per page
     * @return array
     */
    public function listStorage(array $filters = [], int $page = 1, int $perPage = 50): array
    {
        return $this->makeRequest('index.php?act=storage', [
            'page' => $page,
            'reslen' => $perPage,
            ...$filters
        ]);
    }

    /**
     * Edit storage configuration
     *
     * @param int $storageId Storage ID to edit
     * @param array{
     *    name: string,
     *    oversell?: int,
     *    alert_threshold?: int,
     *    primary_storage?: int
     * } $params Storage parameters
     * @return array
     */
    public function editStorage(int $storageId, array $params): array
    {
        return $this->makeRequest("index.php?act=editstorage&stid={$storageId}", $params, 'POST');
    }

    /**
     * Delete storage(s)
     *
     * @param int|string|array $storageIds Single storage ID, comma-separated IDs, or array of IDs
     * @return array API response
     */
    public function deleteStorage($storageIds): array
    {
        $delete = is_array($storageIds) ? implode(',', $storageIds) : $storageIds;
        
        return $this->makeRequest('index.php?act=storage', [
            'delete' => $delete
        ], 'POST');
    }

    /**
     * Add new storage
     *
     * @param array{
     *    name: string,
     *    path: string,
     *    type: string,
     *    serid: int|array,
     *    format: string,
     *    primary_storage?: int,
     *    oversell?: int,
     *    alert_threshold?: int,
     *    lightbit_project?: string
     * } $params Storage parameters
     * @return array
     */
    public function addStorage(array $params): array
    {
        return $this->makeRequest('index.php?act=addstorage', $params, 'POST');
    }

    /**
     * List orphaned disks
     *
     * @param array{
     *    st_id?: int,
     *    st_type?: string,
     *    disk_path?: string
     * } $filters Search filters
     * @return array
     */
    public function listOrphanedDisks(array $filters = []): array
    {
        return $this->makeRequest('index.php?act=orphaneddisks', $filters);
    }

    /**
     * Manage orphaned disks
     *
     * @param array{delete?: string} $post Post parameters
     * @return array API response
     * @throws VirtualizorApiException
     */
    public function orphaneddisks(array $post = []): array
    {
        $path = 'index.php?act=orphaneddisks';
        return $this->call($path, [], $post);
    }

    /**
     * List volumes
     *
     * @param array{
     *    name?: string,
     *    path?: string,
     *    user_email?: string,
     *    search?: string
     * } $filters Search filters
     * @param int $page Page number
     * @param int $perPage Records per page
     * @return array API response
     */
    public function listVolumes(array $filters = [], int $page = 1, int $perPage = 50): array
    {
        return $this->makeRequest('index.php?act=volumes', [
            'page' => $page,
            'reslen' => $perPage,
            ...$filters
        ]);
    }

    /**
     * Add a new volume
     *
     * @param array{
     *    disk_name: string,
     *    vpsid: int,
     *    newserid: int,
     *    size: float,
     *    format_type: string,
     *    attach?: int,
     *    mnt_point?: string,
     *    st_uuid?: string
     * } $params Volume parameters
     * @return array API response
     */
    public function addVolume(array $params): array
    {
        return $this->makeRequest('index.php?act=volumes', [
            'addvolume' => 1,
            ...$params
        ], 'POST');
    }

    /**
     * Edit volume
     *
     * @param array{
     *    e_serid: int,
     *    e_vpsid: int,
     *    e_todo: int,
     *    e_disk_size?: float,
     *    disk_did_action: int
     * } $params Volume edit parameters
     * @return array API response
     */
    public function editVolume(array $params): array
    {
        return $this->makeRequest('index.php?act=volumes', $params, 'POST');
    }

    /**
     * Delete volume(s)
     *
     * @param int|string|array $volumeIds Single volume ID, comma-separated IDs, or array of IDs
     * @return array API response
     */
    public function deleteVolumes($volumeIds): array
    {
        $delete = is_array($volumeIds) ? implode(',', $volumeIds) : $volumeIds;
        
        return $this->makeRequest('index.php?act=volumes', [
            'delete' => $delete
        ], 'POST');
    }

    /**
     * List plans
     *
     * @param array{
     *    planname?: string,
     *    ptype?: string
     * } $filters Search filters
     * @param int $page Page number
     * @param int $perPage Records per page
     * @return array API response
     */
    public function listPlans(array $filters = [], int $page = 1, int $perPage = 50): array
    {
        return $this->makeRequest('index.php?act=plans', [
            'page' => $page,
            'reslen' => $perPage,
            ...$filters
        ]);
    }

    /**
     * Add a new plan
     *
     * @param array{
     *    plan_name: string,
     *    cplan: string,
     *    disk_space: int,
     *    guaranteed_ram: int,
     *    swapram: int,
     *    burst_ram?: int,
     *    bandwidth: int,
     *    network_speed?: int,
     *    cpu_units: int,
     *    cpu_cores: int,
     *    percent_cpu: int,
     *    priority?: int,
     *    ips?: int,
     *    ips6_subnet?: int,
     *    ips6?: int,
     *    ips_int?: int,
     *    ploop?: int,
     *    virtio?: int,
     *    acpi?: int,
     *    apic?: int,
     *    pae?: int,
     *    mgs?: array,
     *    upload_speed?: int,
     *    band_suspend?: int,
     *    dns?: array,
     *    ippoolid?: array,
     *    nic_type?: string,
     *    control_panel?: string,
     *    recipe?: int,
     *    cpu_mode?: string,
     *    enable_cpu_topology?: int,
     *    topology_sockets?: int,
     *    topology_cores?: int,
     *    topology_threads?: int,
     *    vnc_keymap?: string,
     *    kvm_cache?: string,
     *    io_mode?: string,
     *    kvm_vga?: int,
     *    acceleration?: int,
     *    total_iops_sec?: int,
     *    read_bytes_sec?: int,
     *    write_bytes_sec?: int,
     *    rdp?: int,
     *    osreinstall_limit?: int,
     *    admin_managed?: int,
     *    disable_nw_config?: int,
     *    tuntap?: int,
     *    ppp?: int,
     *    fuse?: int,
     *    ipip?: int,
     *    ipgre?: int,
     *    nfs?: int,
     *    quotaugidlimit?: int,
     *    iolimit?: int,
     *    iopslimit?: int,
     *    install_xentools?: int,
     *    shadow?: int,
     *    pv_on_hvm?: int,
     *    vif_type?: string,
     *    numa?: int,
     *    os_type?: string,
     *    rtc?: int,
     *    ssd_emulation?: int,
     *    disable_password?: int,
     *    discard?: int,
     *    enable_ver_scaling?: int,
     *    ver_max_ram?: int,
     *    ver_ram_threshold?: int,
     *    ver_ram_inc_by?: int,
     *    ver_max_cpu?: int,
     *    ver_cpu_threshold?: int,
     *    ver_cpu_inc_by?: int
     * } $params Plan parameters
     * @return array API response
     */
    public function addPlan(array $params): array
    {
        return $this->makeRequest('index.php?act=addplan', $params, 'POST');
    }

    /**
     * Edit plan
     *
     * @param int $planId Plan ID to edit
     * @param array $params Plan parameters
     * @return array API response
     */
    public function editPlan(int $planId, array $params): array
    {
        return $this->makeRequest("index.php?act=editplan&plid={$planId}", $params, 'POST');
    }

    /**
     * Delete plan(s)
     *
     * @param int|string|array $planIds Single plan ID, comma-separated IDs, or array of IDs
     * @return array API response
     */
    public function deletePlans($planIds): array
    {
        $delete = is_array($planIds) ? implode(',', $planIds) : $planIds;
        
        return $this->makeRequest('index.php?act=plans', [
            'delete' => $delete
        ], 'POST');
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
     * @return array API response
     */
    public function listUserPlans(array $filters = [], int $page = 1, int $perPage = 50): array
    {
        return $this->makeRequest('index.php?act=user_plans', [
            'page' => $page,
            'reslen' => $perPage,
            ...$filters
        ]);
    }

    /**
     * Add user plan
     *
     * @param array{
     *    plan_name: string,
     *    priority: int,
     *    dnsplan_id?: int,
     *    acl_id?: int,
     *    num_vs?: int,
     *    inhouse_billing?: int,
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
     *    band_suspend?: int,
     *    service_period?: int,
     *    allowed_virts?: array,
     *    sgs?: array,
     *    mgs?: array,
     *    space_per_vm?: int,
     *    total_iops_sec?: int,
     *    read_bytes_sec?: int,
     *    write_bytes_sec?: int
     * } $params Plan parameters
     * @return array API response
     */
    public function addUserPlan(array $params): array
    {
        return $this->makeRequest('index.php?act=adduser_plans', $params, 'POST');
    }

    /**
     * Edit user plan
     *
     * @param int $planId User plan ID to edit
     * @param array $params Plan parameters
     * @return array API response
     */
    public function editUserPlan(int $planId, array $params): array
    {
        return $this->makeRequest("index.php?act=edituser_plans&uplid={$planId}", $params, 'POST');
    }

    /**
     * List DNS plans
     *
     * @param array{
     *    planname?: string
     * } $filters Search filters
     * @param int $page Page number
     * @param int $perPage Records per page
     * @return array API response
     */
    public function listDnsPlans(array $filters = [], int $page = 1, int $perPage = 50): array
    {
        return $this->makeRequest('index.php?act=dnsplans', [
            'page' => $page,
            'reslen' => $perPage,
            ...$filters
        ]);
    }

    /**
     * Add DNS plan
     *
     * @param array{
     *    plan_name: string,
     *    dnsserverid: int,
     *    maxdomains: int,
     *    maxdomainsrec: int,
     *    ttl: int
     * } $params DNS plan parameters
     * @return array API response
     */
    public function addDnsPlan(array $params): array
    {
        return $this->makeRequest('index.php?act=adddnsplans', $params, 'POST');
    }

    /**
     * Edit DNS plan
     *
     * @param int $planId DNS plan ID to edit
     * @param array{
     *    plan_name: string,
     *    dnsserverid: int,
     *    maxdomains: int,
     *    maxdomainsrec: int,
     *    ttl: int
     * } $params DNS plan parameters
     * @return array API response
     */
    public function editDnsPlan(int $planId, array $params): array
    {
        return $this->makeRequest("index.php?act=editdnsplans&dnsplid={$planId}", $params, 'POST');
    }

    /**
     * Delete DNS plan(s)
     *
     * @param int|string|array $planIds Single plan ID, comma-separated IDs, or array of IDs
     * @return array API response
     */
    public function deleteDnsPlans($planIds): array
    {
        $delete = is_array($planIds) ? implode(',', $planIds) : $planIds;
        
        return $this->makeRequest('index.php?act=dnsplans', [
            'delete' => $delete
        ], 'POST');
    }

    /**
     * List backup plans
     *
     * @param array{
     *    planname?: string
     * } $filters Search filters
     * @param int $page Page number
     * @param int $perPage Records per page
     * @return array API response
     */
    public function listBackupPlans(array $filters = [], int $page = 1, int $perPage = 50): array
    {
        return $this->makeRequest('index.php?act=backup_plans', [
            'page' => $page,
            'reslen' => $perPage,
            ...$filters
        ]);
    }

    /**
     * Delete user plan(s)
     *
     * @param int|string|array $planIds Single plan ID, comma-separated IDs, or array of IDs
     * @return array API response
     */
    public function deleteUserPlans($planIds): array
    {
        $delete = is_array($planIds) ? implode(',', $planIds) : $planIds;
        
        return $this->makeRequest('index.php?act=user_plans', [
            'delete' => $delete
        ], 'POST');
    }

    /**
     * Add backup plan
     *
     * @param array{
     *    disabled?: int,
     *    plan_name: string,
     *    type: string,
     *    id?: int,
     *    dir: string,
     *    freq: string,
     *    hourly_freq: int,
     *    hrs: int,
     *    min: int,
     *    day: int,
     *    date: int,
     *    rotation: int,
     *    backup_limit: int,
     *    restore_limit: int,
     *    nice: int,
     *    ionice_prio: int,
     *    ionice_class: int,
     *    compression?: int
     * } $params Backup plan parameters
     * @return array API response
     */
    public function addBackupPlan(array $params): array
    {
        return $this->makeRequest('index.php?act=addbackup_plan', $params, 'POST');
    }

    /**
     * Edit backup plan
     *
     * @param int $planId Backup plan ID to edit
     * @param array{
     *    disabled?: int,
     *    plan_name: string,
     *    type: string,
     *    id?: int,
     *    dir: string,
     *    freq: string,
     *    hourly_freq: int,
     *    hrs: int,
     *    min: int,
     *    day: int,
     *    date: int,
     *    rotation: int,
     *    backup_limit: int,
     *    restore_limit: int,
     *    nice: int,
     *    ionice_prio: int,
     *    ionice_class: int,
     *    compression?: int
     * } $params Backup plan parameters
     * @return array API response
     */
    public function editBackupPlan(int $planId, array $params): array
    {
        return $this->makeRequest('index.php?act=editbackup_plan', [
            'bpid' => $planId,
            ...$params
        ], 'POST');
    }

    /**
     * Delete backup plan(s)
     *
     * @param int|string|array $planIds Single plan ID, comma-separated IDs, or array of IDs
     * @return array API response
     */
    public function deleteBackupPlans($planIds): array
    {
        $delete = is_array($planIds) ? implode(',', $planIds) : $planIds;
        
        return $this->makeRequest('index.php?act=backup_plans', [
            'delete' => $delete
        ], 'POST');
    }

    /**
     * List login logs
     *
     * @param array{
     *    username?: string,
     *    ip?: string
     * } $filters Search filters
     * @param int $page Page number
     * @param int $perPage Records per page
     * @return array API response
     */
    public function listLoginLogs(array $filters = [], int $page = 1, int $perPage = 50): array
    {
        return $this->makeRequest('index.php?act=loginlogs', [
            'page' => $page,
            'reslen' => $perPage,
            ...$filters
        ]);
    }

    /**
     * List user activity logs
     *
     * @param array{
     *    vpsid?: int,
     *    email?: string
     * } $filters Search filters
     * @param int $page Page number
     * @param int $perPage Records per page
     * @return array API response
     */
    public function listUserLogs(array $filters = [], int $page = 1, int $perPage = 50): array
    {
        return $this->makeRequest('index.php?act=userlogs', [
            'page' => $page,
            'reslen' => $perPage,
            ...$filters
        ]);
    }

    /**
     * List IP logs
     *
     * @param array{
     *    ip?: string,
     *    vpsid?: int
     * } $filters Search filters
     * @param int $page Page number
     * @param int $perPage Records per page
     * @return array API response
     */
    public function listIpLogs(array $filters = [], int $page = 1, int $perPage = 50): array
    {
        return $this->makeRequest('index.php?act=iplogs', [
            'page' => $page,
            'reslen' => $perPage,
            ...$filters
        ]);
    }

    /**
     * Delete IP logs
     *
     * @return array API response
     */
    public function deleteIpLogs(): array
    {
        return $this->makeRequest('index.php?act=iplogs', [
            'delete' => 1
        ], 'POST');
    }

    /**
     * List services
     *
     * @param array{
     *    changeserid?: int
     * } $params Query parameters
     * @return array API response
     */
    public function listServices(array $params = []): array
    {
        return $this->makeRequest('index.php?act=services', $params);
    }

    /**
     * Manage services
     *
     * @param array{
     *    sel_serv: array,
     *    action: string,
     *    changeserid?: int
     * } $params Service management parameters
     * @return array API response
     */
    public function manageServices(array $params): array
    {
        return $this->makeRequest('index.php?act=services', $params, 'POST');
    }

    /**
     * Restart service
     *
     * @param array{
     *    service: string,
     *    changeserid?: int
     * } $params Service parameters
     * @return array API response
     */
    public function restartService(array $params): array
    {
        return $this->makeRequest('index.php?act=restartservices', $params, 'POST');
    }

    /**
     * Get VPS statistics
     *
     * @param array{
     *    vpsid?: int,
     *    serid?: int,
     *    svs?: int,
     *    show?: string
     * } $params Query parameters
     * @return array API response
     */
    public function getVpsStats(array $params = []): array
    {
        return $this->makeRequest('index.php?act=vps_stats', $params);
    }

    /**
     * List processes
     *
     * @param array{
     *    changeserid?: int
     * } $params Query parameters
     * @return array API response
     */
    public function listProcesses(array $params = []): array
    {
        return $this->makeRequest('index.php?act=processes', $params);
    }

    /**
     * Kill processes
     *
     * @param array $processIds Array of process IDs to kill
     * @param array{
     *    changeserid?: int
     * } $params Additional parameters
     * @return array API response
     */
    public function killProcesses(array $processIds, array $params = []): array
    {
        return $this->makeRequest('index.php?act=processes', [
            'sel_proc' => $processIds,
            'action' => 'kill',
            ...$params
        ], 'POST');
    }

    /**
     * Get disk information
     *
     * @param array{
     *    changeserid?: int
     * } $params Query parameters
     * @return array API response
     */
    public function getDiskInfo(array $params = []): array
    {
        return $this->makeRequest('index.php?act=manageserver', $params);
    }

    /**
     * Get bandwidth usage
     *
     * @param string $month Month in YYYYMM format
     * @return array API response
     */
    public function getBandwidth(string $month): array
    {
        return $this->makeRequest('index.php?act=bandwidth', [
            'show' => $month
        ]);
    }

    /**
     * Get firewall status
     *
     * @return array API response
     */
    public function firewallStatus(): array
    {
        return $this->makeRequest('index.php?act=firewall', [
            'status' => 1
        ]);
    }

    /**
     * Start firewall service
     *
     * @return array API response
     */
    public function firewallStart(): array
    {
        return $this->makeRequest('index.php?act=firewall', [
            'start' => 1
        ]);
    }

    /**
     * Stop firewall service
     *
     * @return array API response
     */
    public function firewallStop(): array
    {
        return $this->makeRequest('index.php?act=firewall', [
            'stop' => 1
        ]);
    }

    /**
     * Restart firewall service
     *
     * @return array API response
     */
    public function firewallRestart(): array
    {
        return $this->makeRequest('index.php?act=firewall', [
            'restart' => 1
        ]);
    }

    /**
     * Block port(s)
     *
     * @param string $ports Comma-separated ports to block
     * @return array API response
     */
    public function firewallBlockPort(string $ports): array
    {
        return $this->makeRequest('index.php?act=firewall', [
            'block' => 1,
            'port' => $ports
        ]);
    }

    /**
     * Allow port(s)
     *
     * @param string $ports Comma-separated ports to allow
     * @return array API response
     */
    public function firewallAllowPort(string $ports): array
    {
        return $this->makeRequest('index.php?act=firewall', [
            'allow' => 1,
            'port' => $ports
        ]);
    }

    /**
     * Block IP address
     *
     * @param array{
     *    ip: string,
     *    ip_port?: string,
     *    ip_temp?: int
     * } $params IP parameters
     * @return array API response
     */
    public function firewallBlockIp(array $params): array
    {
        return $this->makeRequest('index.php?act=firewall', [
            'ip_block' => 1,
            ...$params
        ]);
    }

    /**
     * Allow IP address
     *
     * @param array{
     *    ip: string,
     *    ip_port?: string,
     *    ip_temp?: int
     * } $params IP parameters
     * @return array API response
     */
    public function firewallAllowIp(array $params): array
    {
        return $this->makeRequest('index.php?act=firewall', [
            'ip_allow' => 1,
            ...$params
        ]);
    }

    /**
     * Search for IP rules
     *
     * @param string $ip IP address to search for
     * @return array API response
     */
    public function firewallSearchIp(string $ip): array
    {
        return $this->makeRequest('index.php?act=firewall', [
            'search' => 1,
            'ip' => $ip
        ]);
    }

    /**
     * View current firewall rules
     *
     * @return array API response
     */
    public function firewallViewRules(): array
    {
        return $this->makeRequest('index.php?act=firewall', [
            'view' => 1
        ]);
    }

    /**
     * Toggle firewall testing mode
     *
     * @param bool $enable Enable or disable testing mode
     * @return array API response
     */
    public function firewallToggleTestingMode(bool $enable): array
    {
        return $this->makeRequest('index.php?act=firewall', [
            'firewall_testing' => $enable ? 1 : 0
        ]);
    }

    /**
     * Get server monitoring information
     *
     * @param int $serverId Server ID to monitor
     * @param string|null $mode Monitoring mode (default, live_stats, network_stats)
     * @return array API response
     */
    public function getServerMonitoring(int $serverId, ?string $mode = null): array
    {
        $params = ['changeserid' => $serverId];
        if ($mode !== null) {
            $params['your_preference'] = $mode;
        }

        return $this->makeRequest('index.php?act=performance', $params);
    }

    /**
     * Generate new SSH key pair
     *
     * @return array API response
     */
    public function generateSshKeys(): array
    {
        return $this->makeRequest('index.php?act=addvs&generate_keys=1');
    }

    /**
     * Get RAM information
     *
     * @param array{
     *    changeserid?: int
     * } $params Query parameters
     * @return array API response
     */
    public function getRamInfo(array $params = []): array
    {
        return $this->makeRequest('index.php?act=manageserver', $params);
    }

    /**
     * Get CPU information
     *
     * @param array{
     *    changeserid?: int
     * } $params Query parameters
     * @return array API response
     */
    public function getCpuInfo(array $params = []): array
    {
        return $this->makeRequest('index.php?act=manageserver', $params);
    }

    /**
     * Show tasks
     *
     * @return array API response
     */
    public function showTasks(): array
    {
        return $this->makeRequest('index.php?act=tasks');
    }

    /**
     * Show task logs
     *
     * @param int $taskId Task ID to show logs for
     * @return array API response
     */
    public function showTaskLogs(int $taskId): array
    {
        return $this->makeRequest('index.php?act=tasks', [
            'show_logs' => $taskId
        ]);
    }

    /**
     * Search tasks
     *
     * @param array{
     *    actid?: int,
     *    vpsid?: int,
     *    username?: string,
     *    action?: string,
     *    status?: int,
     *    order?: string
     * } $filters Search filters
     * @param int $page Page number
     * @param int $perPage Records per page
     * @return array API response
     */
    public function searchTasks(array $filters = [], int $page = 1, int $perPage = 50): array
    {
        return $this->makeRequest('index.php?act=tasks', [
            'page' => $page,
            'reslen' => $perPage,
            ...$filters
        ]);
    }

    /**
     * Get node performance statistics
     *
     * @param array{
     *    show?: string,
     *    serid?: int
     * } $params Query parameters
     * @return array API response
     */
    public function getNodePerformance(array $params = []): array
    {
        return $this->makeRequest('index.php?act=server_stats', $params);
    }

    /**
     * Get SSL files configuration
     *
     * @return array API response
     */
    public function getSslFiles(): array
    {
        return $this->makeRequest('index.php?act=ssl');
    }

    /**
     * Create SSL certificate
     *
     * @param array{
     *    country: string,
     *    state: string,
     *    locality: string,
     *    organisation: string,
     *    comname: string,
     *    email: string,
     *    keysize: int,
     *    orgunit?: string
     * } $params SSL parameters
     * @return array API response
     */
    public function createSsl(array $params): array
    {
        return $this->makeRequest('index.php?act=createssl', [
            'create' => 1,
            ...$params
        ], 'POST');
    }

    /**
     * Install Let's Encrypt certificate
     *
     * @param array{
     *    primary_domain: string,
     *    contact_email: string,
     *    key_size: int|string,
     *    renew_days: int,
     *    staging?: int,
     *    enable_force?: int,
     *    ssl_type?: string
     * } $params Certificate parameters
     * @return array API response
     */
    public function installLetsEncrypt(array $params): array
    {
        return $this->makeRequest('index.php?act=letsencrypt', [
            'opt' => 'save_cfg',
            ...$params
        ], 'POST');
    }

    /**
     * Renew Let's Encrypt certificate
     *
     * @return array API response
     */
    public function renewLetsEncrypt(): array
    {
        return $this->makeRequest('index.php?act=letsencrypt', [
            'opt' => 'renew_crt'
        ], 'POST');
    }

    /**
     * Show Let's Encrypt logs
     *
     * @return array API response
     */
    public function showLetsEncryptLogs(): array
    {
        return $this->makeRequest('index.php?act=letsencrypt', [
            'opt' => 'show_logs'
        ], 'POST');
    }
}
