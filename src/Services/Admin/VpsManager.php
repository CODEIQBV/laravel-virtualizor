<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

class VpsManager
{
    public function __construct(
        private readonly AdminApi $api
    ) {}

    /**
     * List virtual servers
     *
     * @param  array  $filters  Search filters
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
     * @param  int  $perPage  Records per page
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function list(array $filters = [], int $page = 1, int $perPage = 50, bool $raw = false): array
    {
        // Validate virtualization type if provided
        if (isset($filters['vstype'])) {
            $validTypes = ['xen', 'xenhvm', 'xcp', 'xcphvm', 'openvz', 'kvm', 'lxc', 'vzo', 'vzk', 'proxo', 'proxk', 'proxl'];
            if (! in_array($filters['vstype'], $validTypes)) {
                throw new VirtualizorApiException('Invalid virtualization type');
            }
        }

        // Validate status if provided
        if (isset($filters['vsstatus']) && ! in_array($filters['vsstatus'], ['s', 'u'])) {
            throw new VirtualizorApiException('Invalid status value. Use "s" for suspended or "u" for unsuspended');
        }

        // Validate speed cap if provided
        if (isset($filters['speedcap']) && ! in_array($filters['speedcap'], ['1', '2'])) {
            throw new VirtualizorApiException('Invalid speed cap value. Use "1" for enabled or "2" for disabled');
        }

        try {
            $response = $this->api->listVirtualServers($filters, $page, $perPage);

            if ($raw) {
                return $response;
            }

            // Get the VPS list from the 'vs' key and map it
            return array_map(function ($vps) {
                return [
                    'id' => (int) $vps['vpsid'],
                    'name' => $vps['vps_name'],
                    'uuid' => $vps['uuid'],
                    'server_id' => (int) $vps['serid'],
                    'created_at' => (int) $vps['time'],
                    'updated_at' => (int) $vps['edittime'],
                    'type' => $vps['virt'],
                    'user_id' => (int) $vps['uid'],
                    'plan_id' => (int) $vps['plid'],
                    'hostname' => $vps['hostname'],
                    'os' => [
                        'id' => (int) $vps['osid'],
                        'name' => $vps['os_name'],
                        'distro' => $vps['os_distro'],
                    ],
                    'resources' => [
                        'space' => (int) $vps['space'],
                        'inodes' => (int) $vps['inodes'],
                        'ram' => (int) $vps['ram'],
                        'burst' => (int) $vps['burst'],
                        'swap' => (int) $vps['swap'],
                        'cpu' => (int) $vps['cpu'],
                        'cores' => (int) $vps['cores'],
                        'cpu_percent' => (float) $vps['cpu_percent'],
                        'bandwidth' => (int) $vps['bandwidth'],
                        'network_speed' => (int) $vps['network_speed'],
                        'upload_speed' => (int) $vps['upload_speed'],
                        'io' => (int) $vps['io'],
                    ],
                    'status' => [
                        'suspended' => (bool) $vps['suspended'],
                        'suspend_reason' => $vps['suspend_reason'],
                        'network_suspended' => (bool) $vps['nw_suspended'],
                        'bandwidth_suspended' => (bool) $vps['band_suspend'],
                    ],
                    'network' => [
                        'mac' => $vps['mac'],
                        'ips' => $vps['ips'] ?? [],
                    ],
                    'settings' => [
                        'vnc_enabled' => (bool) $vps['vnc'],
                        'vnc_port' => (int) $vps['vncport'],
                        'vnc_password' => $vps['vnc_passwd'],
                        'vnc_keymap' => $vps['vnc_keymap'],
                        'routing' => (bool) $vps['routing'],
                        'admin_managed' => (bool) $vps['admin_managed'],
                        'locked' => $vps['locked'],
                        'speed_cap' => $vps['speed_cap'],
                        'timezone' => $vps['timezone'],
                    ],
                ];
            }, $response['vs'] ?? []);
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to list virtual servers: '.$e->getMessage(),
                $e->getContext()
            );
        }
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
     * } $vpsData VPS creation data
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function create(array $vpsData, bool $raw = false): array
    {
        // Validate required fields
        $required = ['virt', 'user_email', 'user_pass', 'hostname', 'rootpass', 'osid', 'ips', 'space', 'ram', 'bandwidth', 'cores'];
        foreach ($required as $field) {
            if (! isset($vpsData[$field])) {
                throw new VirtualizorApiException("$field is required");
            }
        }

        // Validate virtualization type
        $validTypes = ['xen', 'xenhvm', 'xcp', 'xcphvm', 'openvz', 'kvm', 'lxc', 'vzo', 'vzk', 'proxo', 'proxk', 'proxl'];
        if (! in_array($vpsData['virt'], $validTypes)) {
            throw new VirtualizorApiException('Invalid virtualization type');
        }

        // Validate bus driver for Proxmox KVM
        if (in_array($vpsData['virt'], ['proxk']) && (! isset($vpsData['bus_driver']) || ! isset($vpsData['bus_driver_num']))) {
            throw new VirtualizorApiException('bus_driver and bus_driver_num are required for Proxmox KVM');
        }

        try {
            $response = $this->api->addVs($vpsData);

            if ($raw) {
                return $response;
            }

            if (! empty($response['vs_info'])) {
                return [
                    'success' => true,
                    'vps' => [
                        'id' => (int) $response['vs_info']['vpsid'],
                        'uuid' => $response['vs_info']['uuid'],
                        'name' => $response['vs_info']['vps_name'],
                        'hostname' => $response['vs_info']['hostname'],
                        'user_id' => (int) $response['vs_info']['uid'],
                        'resources' => [
                            'ram' => (int) $response['vs_info']['ram'],
                            'swap' => (int) $response['vs_info']['swap'],
                            'space' => (int) $response['vs_info']['space'],
                            'cores' => (int) $response['vs_info']['cores'],
                            'cpu' => (int) $response['vs_info']['cpu'],
                            'bandwidth' => (int) $response['vs_info']['bandwidth'],
                            'network_speed' => (int) $response['vs_info']['network_speed'],
                            'upload_speed' => (int) $response['vs_info']['upload_speed'],
                        ],
                        'network' => [
                            'ips' => $response['vs_info']['ips'] ?? [],
                            'ipv6' => $response['vs_info']['ipv6'] ?? [],
                            'ipv6_subnets' => $response['vs_info']['ipv6_subnet'] ?? [],
                            'internal_ips' => $response['vs_info']['ips_int'] ?? [],
                        ],
                        'disks' => $response['vs_info']['disks'] ?? [],
                    ],
                ];
            }

            throw new VirtualizorApiException('Failed to create VPS: Unknown error');
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to create VPS: '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Delete a Virtual Private Server
     *
     * @param  int  $vpsId  VPS ID to delete
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function delete(int $vpsId, bool $raw = false): array
    {
        try {
            $response = $this->api->deleteVs($vpsId);

            if ($raw) {
                return $response;
            }

            // Check for API error messages
            if (! empty($response['error'])) {
                $errorMsg = is_array($response['error'])
                    ? implode(', ', $response['error'])
                    : $response['error'];
                throw new VirtualizorApiException("Failed to delete VPS: {$errorMsg}");
            }

            // Check if operation was successful
            if (! isset($response['done']) || $response['done'] !== true) {
                // Check for any additional error information
                $error = '';
                if (! empty($response['vs'])) {
                    $error = ' VPS status: '.json_encode($response['vs']);
                }
                if (! empty($response['status'])) {
                    $error .= ' Status: '.json_encode($response['status']);
                }
                throw new VirtualizorApiException('Failed to delete VPS: Operation unsuccessful'.$error);
            }

            return [
                'success' => true,
                'id' => $vpsId,
                'timestamp' => $response['timenow'] ?? null,
                'servers' => $response['servers'] ?? null,
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to delete VPS {$vpsId}: ".$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Manage a Virtual Private Server
     *
     * @param  int  $vpsId  VPS ID to manage
     * @param array{
     *    uid?: int,
     *    user_email?: string,
     *    user_pass?: string,
     *    fname?: string,
     *    lname?: string,
     *    dnsplid?: int,
     *    plid?: int,
     *    hostname?: string,
     *    rootpass?: string,
     *    ips?: array,
     *    ips6?: array,
     *    num_ips?: int,
     *    num_ips6?: int,
     *    space?: array,
     *    bus_driver?: string,
     *    bus_driver_num?: int,
     *    ram?: int,
     *    swapram?: int,
     *    cpu?: int,
     *    cores?: int,
     *    vnc?: int,
     *    vncpass?: string,
     *    acpi?: int,
     *    apic?: int,
     *    pae?: int,
     *    boot?: string,
     *    disable_ebtables?: int,
     *    rdp?: int,
     *    admin_managed?: int,
     *    disable_nw_config?: int,
     *    cpupin?: array,
     *    cpu_mode?: string,
     *    topology_sockets?: int,
     *    topology_cores?: int,
     *    topology_threads?: int,
     *    mgs?: array,
     *    osreinstall_limit?: int,
     *    bandwidth?: int,
     *    network_speed?: int,
     *    upload_speed?: int,
     *    speed_cap_up?: int,
     *    speed_cap_down?: int,
     *    band_suspend?: int,
     *    ips_int?: array,
     *    num_ips_int?: int,
     *    mac?: string,
     *    vif_type?: string,
     *    nic_type?: string,
     *    dns?: array,
     *    virtio?: int,
     *    kvm_cache?: string,
     *    io_mode?: string,
     *    kvm_vga?: int,
     *    acceleration?: int,
     *    notes?: string,
     *    enable_rescue?: int,
     *    rescue_pass?: string,
     *    conf_rescue_pass?: string,
     *    disable_rescue?: int,
     *    cpu_percent?: int,
     *    shadow?: int,
     *    iso?: string,
     *    sec_iso?: string,
     *    pv_on_hvm?: int,
     *    tuntap?: int,
     *    ppp?: int,
     *    fuse?: int,
     *    ipip?: int,
     *    ipgre?: int,
     *    nfs?: int,
     *    quotaugidlimit?: int,
     *    iolimit?: int,
     *    iopslimit?: int,
     *    apply_plan?: int,
     *    managevdf?: int,
     *    hvm?: int,
     *    io?: int,
     *    burst?: int,
     *    bpid?: int,
     *    total_iops_sec?: int,
     *    read_bytes_sec?: int,
     *    write_bytes_sec?: int,
     *    os_type?: string,
     *    rtc?: int,
     *    ssd_emulation?: int,
     *    create_inf?: int,
     *    disable_guest_agent?: int,
     *    enable_ver_scaling?: int,
     *    ver_max_ram?: int,
     *    ver_ram_threshold?: int,
     *    ver_ram_inc_by?: int,
     *    ver_max_cpu?: int,
     *    ver_cpu_threshold?: int,
     *    ver_cpu_inc_by?: int
     * } $data VPS management data
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function manage(int $vpsId, array $data, bool $raw = false): array
    {
        // Validate bus driver for Proxmox KVM if provided
        if (isset($data['bus_driver'])) {
            $validBusDrivers = ['sata', 'ide', 'virtio', 'scsi'];
            if (! in_array($data['bus_driver'], $validBusDrivers)) {
                throw new VirtualizorApiException('Invalid bus driver. Must be one of: '.implode(', ', $validBusDrivers));
            }
        }

        // Validate CPU mode if provided
        if (isset($data['cpu_mode'])) {
            $validCpuModes = [
                'host-model', 'host-passthrough', '486', 'athlon', 'pentium', 'pentium2',
                'pentium3', 'coreduo', 'core2duo', 'kvm32', 'qemu32', 'qemu64', 'phenom',
                'Conroe', 'Penryn', 'Nehalem', 'SandyBridge', 'IvyBridge', 'Haswell',
                'Broadwell', 'Opteron_G1', 'Opteron_G2', 'Opteron_G3', 'Opteron_G4', 'Opteron_G5',
            ];
            if (! in_array($data['cpu_mode'], $validCpuModes)) {
                throw new VirtualizorApiException('Invalid CPU mode');
            }
        }

        // Validate os_type if provided
        if (isset($data['os_type'])) {
            $validOsTypes = [
                'other', 'wxp', 'w2k', 'w2k3', 'w2k8', 'wvista', 'win7', 'win8',
                'win10', 'l24', 'l26', 'solaris',
            ];
            if (! in_array($data['os_type'], $validOsTypes)) {
                throw new VirtualizorApiException('Invalid OS type');
            }
        }

        // Validate rescue mode parameters
        if (isset($data['enable_rescue']) && $data['enable_rescue'] === 1) {
            if (empty($data['rescue_pass']) || empty($data['conf_rescue_pass'])) {
                throw new VirtualizorApiException('Rescue password and confirmation are required when enabling rescue mode');
            }
            if ($data['rescue_pass'] !== $data['conf_rescue_pass']) {
                throw new VirtualizorApiException('Rescue passwords do not match');
            }
        }

        try {
            $response = $this->api->manageVps($vpsId, $data);

            if ($raw) {
                return $response;
            }

            // Check for API error messages
            if (! empty($response['error'])) {
                $errorMsg = is_array($response['error'])
                    ? implode(', ', $response['error'])
                    : $response['error'];
                throw new VirtualizorApiException("Failed to manage VPS: {$errorMsg}");
            }

            // Check if operation was successful
            if (! isset($response['done']) || $response['done'] !== true) {
                throw new VirtualizorApiException('Failed to manage VPS: Operation unsuccessful');
            }

            return [
                'success' => true,
                'id' => $vpsId,
                'vps' => $response['vs_info'] ?? null,
                'timestamp' => $response['timenow'] ?? null,
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to manage VPS {$vpsId}: ".$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Start a Virtual Private Server
     *
     * @param  int  $vpsId  VPS ID to start
     * @param  bool  $raw  Return raw API response
     * @return array|bool Returns true/false when raw is false, full response when raw is true
     *
     * @throws VirtualizorApiException
     */
    public function start(int $vpsId, bool $raw = false): array|bool
    {
        try {
            $response = $this->api->startVps($vpsId);

            if ($raw) {
                return $response;
            }

            return ! empty($response['done']);
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to start VPS {$vpsId}: ".$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Stop a Virtual Private Server
     *
     * @param  int  $vpsId  VPS ID to stop
     * @param  bool  $raw  Return raw API response
     * @return array|bool Returns true/false when raw is false, full response when raw is true
     *
     * @throws VirtualizorApiException
     */
    public function stop(int $vpsId, bool $raw = false): array|bool
    {
        try {
            $response = $this->api->stopVps($vpsId);

            if ($raw) {
                return $response;
            }

            return ! empty($response['done']);
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to stop VPS {$vpsId}: ".$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Restart a Virtual Private Server
     *
     * @param  int  $vpsId  VPS ID to restart
     * @param  bool  $raw  Return raw API response
     * @return array|bool Returns true/false when raw is false, full response when raw is true
     *
     * @throws VirtualizorApiException
     */
    public function restart(int $vpsId, bool $raw = false): array|bool
    {
        try {
            $response = $this->api->restartVps($vpsId);

            if ($raw) {
                return $response;
            }

            return ! empty($response['done']);
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to restart VPS {$vpsId}: ".$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Power off a Virtual Private Server
     *
     * @param  int  $vpsId  VPS ID to power off
     * @param  bool  $raw  Return raw API response
     * @return array|bool Returns true/false when raw is false, full response when raw is true
     *
     * @throws VirtualizorApiException
     */
    public function poweroff(int $vpsId, bool $raw = false): array|bool
    {
        try {
            $response = $this->api->poweroffVps($vpsId);

            if ($raw) {
                return $response;
            }

            return ! empty($response['done']);
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to power off VPS {$vpsId}: ".$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Suspend a Virtual Private Server
     *
     * @param  int  $vpsId  VPS ID to suspend
     * @param  string|null  $reason  Reason for suspension
     * @param  bool  $raw  Return raw API response
     * @return array|bool Returns true/false when raw is false, full response when raw is true
     *
     * @throws VirtualizorApiException
     */
    public function suspend(int $vpsId, ?string $reason = null, bool $raw = false): array|bool
    {
        try {
            $params = [];
            if ($reason !== null) {
                $params['suspend_reason'] = $reason;
            }

            $response = $this->api->suspendVps($vpsId, $params);

            if ($raw) {
                return $response;
            }

            return ! empty($response['done']);
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to suspend VPS {$vpsId}: ".$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Unsuspend a Virtual Private Server
     *
     * @param  int  $vpsId  VPS ID to unsuspend
     * @param  bool  $raw  Return raw API response
     * @return array|bool Returns true/false when raw is false, full response when raw is true
     *
     * @throws VirtualizorApiException
     */
    public function unsuspend(int $vpsId, bool $raw = false): array|bool
    {
        try {
            $response = $this->api->unsuspendVps($vpsId);

            if ($raw) {
                return $response;
            }

            return ! empty($response['done']);
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to unsuspend VPS {$vpsId}: ".$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Suspend network for a Virtual Private Server
     *
     * @param  int  $vpsId  VPS ID to suspend network
     * @param  bool  $raw  Return raw API response
     * @return array|bool Returns true/false when raw is false, full response when raw is true
     *
     * @throws VirtualizorApiException
     */
    public function suspendNetwork(int $vpsId, bool $raw = false): array|bool
    {
        try {
            $response = $this->api->suspendNetworkVps($vpsId);

            if ($raw) {
                return $response;
            }

            return ! empty($response['done']);
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to suspend network for VPS {$vpsId}: ".$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Unsuspend network for a Virtual Private Server
     *
     * @param  int  $vpsId  VPS ID to unsuspend network
     * @param  bool  $raw  Return raw API response
     * @return array|bool Returns true/false when raw is false, full response when raw is true
     *
     * @throws VirtualizorApiException
     */
    public function unsuspendNetwork(int $vpsId, bool $raw = false): array|bool
    {
        try {
            $response = $this->api->unsuspendNetworkVps($vpsId);

            if ($raw) {
                return $response;
            }

            return ! empty($response['done']);
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to unsuspend network for VPS {$vpsId}: ".$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Rebuild a Virtual Private Server
     *
     * @param  int  $vpsId  VPS ID to rebuild
     * @param  int  $osId  New OS template ID
     * @param  string  $password  New root password
     * @param array{
     *    format_primary?: bool,
     *    send_email?: bool,
     *    recipe?: int,
     *    sshkey?: string
     * } $options Additional options
     * @param  bool  $raw  Return raw API response
     * @return array|bool Returns true/false when raw is false, full response when raw is true
     *
     * @throws VirtualizorApiException
     */
    public function rebuild(
        int $vpsId,
        int $osId,
        string $password,
        array $options = [],
        bool $raw = false
    ): array|bool {
        try {
            $params = [
                'vpsid' => $vpsId,
                'osid' => $osId,
                'newpass' => $password,
                'conf' => $password, // Confirmation password must match
            ];

            // Add optional parameters
            if (isset($options['format_primary'])) {
                $params['format_primary'] = $options['format_primary'] ? 1 : 0;
            }
            if (isset($options['send_email'])) {
                $params['eu_send_rebuild_email'] = $options['send_email'] ? 1 : 0;
            }
            if (isset($options['recipe'])) {
                $params['recipe'] = $options['recipe'];
            }
            if (isset($options['sshkey'])) {
                $params['sshkey'] = $options['sshkey'];
            }

            $response = $this->api->rebuildVps($params);

            if ($raw) {
                return $response;
            }

            return ! empty($response['done']);
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to rebuild VPS {$vpsId}: ".$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Migrate Virtual Private Server(s)
     *
     * @param  array  $vpsIds  Array of VPS IDs to migrate
     * @param  array  $storageIds  Array of destination storage IDs
     * @param  string  $fromIp  Source server IP
     * @param  string  $fromPass  Source server password
     * @param array{
     *    from_server?: int,
     *    to_server?: int,
     *    speed_limit?: int,
     *    preserve_ip?: bool,
     *    delete_original?: bool,
     *    ignore_vdf_conflict?: bool,
     *    disable_gzip?: bool,
     *    live_migration?: bool
     * } $options Additional migration options
     * @param  bool  $raw  Return raw API response
     * @return array|bool Returns true/false when raw is false, full response when raw is true
     *
     * @throws VirtualizorApiException
     */
    public function migrate(
        array $vpsIds,
        array $storageIds,
        string $fromIp,
        string $fromPass,
        array $options = [],
        bool $raw = false
    ): array|bool {
        try {
            $params = [
                'vpsid' => $vpsIds,
                'storage' => $storageIds,
                'from_ip' => $fromIp,
                'from_pass' => $fromPass,
            ];

            // Add optional parameters
            if (isset($options['from_server'])) {
                $params['from_server'] = $options['from_server'];
            }
            if (isset($options['to_server'])) {
                $params['to_server'] = $options['to_server'];
            }
            if (isset($options['speed_limit'])) {
                $params['speed_limit'] = $options['speed_limit'];
            }
            if (isset($options['preserve_ip'])) {
                $params['preserve_ip'] = $options['preserve_ip'] ? 1 : 0;
            }
            if (isset($options['delete_original'])) {
                $params['del_orig'] = $options['delete_original'] ? 1 : 0;
            }
            if (isset($options['ignore_vdf_conflict'])) {
                $params['ignore_if_vdfconflict'] = $options['ignore_vdf_conflict'] ? 1 : 0;
            }
            if (isset($options['disable_gzip'])) {
                $params['disable_gzip'] = $options['disable_gzip'] ? 1 : 0;
            }
            if (isset($options['live_migration'])) {
                $params['live_mig'] = $options['live_migration'] ? 1 : 0;
            }

            $response = $this->api->migrateVps($params);

            if ($raw) {
                return $response;
            }

            return ! empty($response['actid']);
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to migrate VPS(s): '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Clone Virtual Private Server(s)
     *
     * @param  array  $vpsIds  Array of VPS IDs to clone
     * @param  array  $storageIds  Array of destination storage IDs
     * @param  int|string  $fromServer  Source server ID
     * @param  int  $toServer  Destination server ID
     * @param array{
     *    speed_limit?: int,
     *    ignore_vdf_conflict?: bool,
     *    disable_gzip?: bool,
     *    preserve_ip?: bool
     * } $options Additional clone options
     * @param  bool  $raw  Return raw API response
     * @return array|bool Returns true/false when raw is false, full response when raw is true
     *
     * @throws VirtualizorApiException
     */
    public function clone(
        array $vpsIds,
        array $storageIds,
        int|string $fromServer,
        int $toServer,
        array $options = [],
        bool $raw = false
    ): array|bool {
        try {
            $params = [
                'vpsid' => $vpsIds,
                'storage' => $storageIds,
                'from_server' => $fromServer,
                'to_server' => $toServer,
            ];

            // Add optional parameters
            if (isset($options['speed_limit'])) {
                $params['speed_limit'] = $options['speed_limit'];
            }
            if (isset($options['ignore_vdf_conflict'])) {
                $params['ignore_if_vdfconflict'] = $options['ignore_vdf_conflict'] ? 1 : 0;
            }
            if (isset($options['disable_gzip'])) {
                $params['disable_gzip'] = $options['disable_gzip'] ? 1 : 0;
            }
            if (isset($options['preserve_ip'])) {
                $params['preserve_ip'] = $options['preserve_ip'] ? 1 : 0;
            }

            $response = $this->api->cloneVps($params);

            if ($raw) {
                return $response;
            }

            return ! empty($response['actid']);
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to clone VPS(s): '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Get VNC information for a Virtual Private Server
     *
     * @param  int  $vpsId  VPS ID to get VNC info
     * @param  bool  $raw  Return raw API response
     * @return array Returns VNC connection details
     *
     * @throws VirtualizorApiException
     */
    public function getVncInfo(int $vpsId, bool $raw = false): array
    {
        try {
            $response = $this->api->getVncInfo($vpsId);

            if ($raw) {
                return $response;
            }

            if (empty($response['info'])) {
                throw new VirtualizorApiException('Failed to get VNC info: No information returned');
            }

            return [
                'success' => true,
                'id' => $vpsId,
                'vnc' => [
                    'port' => $response['info']['port'],
                    'ip' => $response['info']['ip'],
                    'password' => $response['info']['password'],
                ],
                'timestamp' => $response['timenow'] ?? null,
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to get VNC info for VPS {$vpsId}: ".$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Add domain forwarding record for a Virtual Private Server
     *
     * @param  string  $vpsUuid  VPS UUID
     * @param  string  $protocol  Protocol (HTTP, HTTPS, TCP)
     * @param  string  $sourceHostname  Source hostname/IP
     * @param  int|string  $sourcePort  Source port
     * @param  string  $destIp  Destination IP
     * @param  int|string  $destPort  Destination port
     * @param  int  $serverId  Server ID (default: 0)
     * @param  bool  $raw  Return raw API response
     * @return array|int Returns record ID when raw is false, full response when raw is true
     *
     * @throws VirtualizorApiException
     */
    public function addDomainForwarding(
        string $vpsUuid,
        string $protocol,
        string $sourceHostname,
        int|string $sourcePort,
        string $destIp,
        int|string $destPort,
        int $serverId = 0,
        bool $raw = false
    ): array|int {
        try {
            // Validate protocol
            $validProtocols = ['HTTP', 'HTTPS', 'TCP'];
            if (! in_array(strtoupper($protocol), $validProtocols)) {
                throw new VirtualizorApiException(
                    'Invalid protocol. Must be one of: '.implode(', ', $validProtocols)
                );
            }

            $params = [
                'serid' => $serverId,
                'vpsuuid' => $vpsUuid,
                'protocol' => strtoupper($protocol),
                'src_hostname' => $sourceHostname,
                'src_port' => $sourcePort,
                'dest_ip' => $destIp,
                'dest_port' => $destPort,
            ];

            $response = $this->api->addDomainForwarding($params);

            if ($raw) {
                return $response;
            }

            if (empty($response['done']['id'])) {
                throw new VirtualizorApiException(
                    'Failed to add domain forwarding record: No record ID returned'
                );
            }

            return (int) $response['done']['id'];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to add domain forwarding record: '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Edit domain forwarding record
     *
     * @param int $recordId Record ID to edit
     * @param string $vpsUuid VPS UUID
     * @param string $protocol Protocol (HTTP, HTTPS, TCP)
     * @param string $sourceHostname Source hostname/IP
     * @param int|string $sourcePort Source port
     * @param string $destIp Destination IP
     * @param int|string $destPort Destination port
     * @param int $serverId Server ID (default: 0)
     * @param bool $raw Return raw API response
     * @return array|bool Returns true/false when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function editDomainForwarding(
        int $recordId,
        string $vpsUuid,
        string $protocol,
        string $sourceHostname,
        int|string $sourcePort,
        string $destIp,
        int|string $destPort,
        int $serverId = 0,
        bool $raw = false
    ): array|bool {
        try {
            // Validate protocol
            $validProtocols = ['HTTP', 'HTTPS', 'TCP'];
            if (!in_array(strtoupper($protocol), $validProtocols)) {
                throw new VirtualizorApiException(
                    'Invalid protocol. Must be one of: ' . implode(', ', $validProtocols)
                );
            }

            $params = [
                'serid' => $serverId,
                'vpsuuid' => $vpsUuid,
                'protocol' => strtoupper($protocol),
                'src_hostname' => $sourceHostname,
                'src_port' => $sourcePort,
                'dest_ip' => $destIp,
                'dest_port' => $destPort
            ];

            $response = $this->api->editDomainForwarding($recordId, $params);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to edit domain forwarding record: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to edit domain forwarding record {$recordId}: " . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Delete domain forwarding record(s)
     *
     * @param int|array $recordIds Single record ID or array of record IDs
     * @param bool $raw Return raw API response
     * @return array|bool Returns true/false when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function deleteDomainForwarding(int|array $recordIds, bool $raw = false): array|bool
    {
        try {
            $response = $this->api->deleteDomainForwarding($recordIds);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to delete domain forwarding record(s): Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            $ids = is_array($recordIds) ? implode(', ', $recordIds) : $recordIds;
            throw new VirtualizorApiException(
                "Failed to delete domain forwarding record(s) {$ids}: " . $e->getMessage(),
                $e->getContext()
            );
        }
    }
}
