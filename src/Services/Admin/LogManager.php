<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

class LogManager
{
    /**
     * Available log actions
     */
    private const LOG_ACTIONS = [
        'addvs' => 'Creating VPS',
        'addvs_restore' => 'Creating VPS for Restore',
        'rebuildvs' => 'Rebuilding VPS',
        'vpsbackups' => 'Backing up VPS',
        'restorevps' => 'Restoring VPS',
        'migrate2' => 'Migrating VPS',
        'multimigrateprog' => 'Multiple Migrate',
        'clone2' => 'Cloning VPS',
        'multicloneprog' => 'Multiple Clone',
        'deletevs' => 'Deleting VPS',
        'createtemplate' => 'Creating Template',
        'fstab_handle' => 'FS Tab Handle',
        'editxcpvs' => 'Edit XCP VPS',
        'resizevps' => 'Resizing VPS',
        'multivirt' => 'Enabling Multivirt',
        'getos' => 'Downloading OS',
        'change_dnsnameserver' => 'Changing DNS Nameserver',
        'changepassword' => 'Changing VPS Password',
        'install_cp' => 'Install Control panel',
        'hostname' => 'Changing VPS Hostname',
        'install_recipe' => 'Install Recipe',
        'bandwidth_unsuspend' => 'Unsuspend Bandwidth',
        'suspend_callback' => 'Suspend Callback',
        'unsuspend_callback' => 'Unsuspend Callback',
        'editvps_callback' => 'Edit VPS Callback',
        'terminate_callback' => 'Terminate VPS Callback',
        'get_crt' => 'Lets Encrypt Install Certificate',
        'renew_crt' => 'Lets Encrypt Renew Certificate',
        'cron_crt' => 'Lets Encrypt Renew Certificate Cron Task',
        'haproxy_cron' => 'HAProxy Rebuild',
        'vpsbackups_plan' => 'VPS Backups',
        'restorevps_plan' => 'VPS Restore',
        'addsshkeys' => 'Adding SSH Keys',
        'installxentools' => 'Installing Xenserver Tools',
        'dbbackups' => 'Database Backup',
        'install_script' => 'Installing Apps'
    ];

    public function __construct(
        private readonly AdminApi $api
    ) {}

    /**
     * List admin activity logs
     *
     * @param array{
     *    id?: int,
     *    email?: string
     * } $filters Search filters
     * @param int $page Page number
     * @param int $perPage Records per page
     * @param bool $raw Return raw API response
     * @return array Returns formatted log info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function adminLogs(array $filters = [], int $page = 1, int $perPage = 50, bool $raw = false): array
    {
        try {
            $response = $this->api->listLogs($filters, $page, $perPage);

            if ($raw) {
                return $response;
            }

            $logs = [];
            foreach ($response['logs'] ?? [] as $id => $log) {
                $logs[] = [
                    'id' => (int) $log['actid'],
                    'user_id' => (int) $log['uid'],
                    'resource_id' => (int) $log['id'],
                    'action' => [
                        'type' => $log['action'],
                        'description' => self::LOG_ACTIONS[$log['action']] ?? $log['action']
                    ],
                    'data' => $log['data'],
                    'status' => (bool) $log['status'],
                    'ip' => $log['ip'],
                    'email' => $log['email'],
                    'timestamp' => (int) $log['time']
                ];
            }

            return [
                'logs' => $logs,
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to list admin logs: ' . $e->getMessage(),
                $e->getContext()
            );
        }
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
     * @param bool $raw Return raw API response
     * @return array Returns formatted log info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function loginLogs(array $filters = [], int $page = 1, int $perPage = 50, bool $raw = false): array
    {
        try {
            $response = $this->api->listLoginLogs($filters, $page, $perPage);

            if ($raw) {
                return $response;
            }

            $logs = [];
            foreach ($response['loginlogs'] ?? [] as $log) {
                $logs[] = [
                    'username' => $log['username'],
                    'vps_id' => (int) $log['vpsid'],
                    'timestamp' => (int) $log['time'],
                    'status' => (bool) $log['status'],
                    'ip' => $log['ip']
                ];
            }

            return [
                'logs' => $logs,
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to list login logs: ' . $e->getMessage(),
                $e->getContext()
            );
        }
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
     * @param bool $raw Return raw API response
     * @return array Returns formatted log info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function userLogs(array $filters = [], int $page = 1, int $perPage = 50, bool $raw = false): array
    {
        try {
            $response = $this->api->listUserLogs($filters, $page, $perPage);

            if ($raw) {
                return $response;
            }

            $logs = [];
            foreach ($response['userlogs'] ?? [] as $id => $log) {
                $logs[] = [
                    'id' => (int) $log['actid'],
                    'user_id' => (int) $log['uid'],
                    'vps_id' => (int) $log['vpsid'],
                    'action' => $log['action'],
                    'data' => $log['data'],
                    'status' => (bool) $log['status'],
                    'ip' => $log['ip'],
                    'email' => $log['email'],
                    'timestamp' => (int) $log['time']
                ];
            }

            return [
                'logs' => $logs,
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to list user logs: ' . $e->getMessage(),
                $e->getContext()
            );
        }
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
     * @param bool $raw Return raw API response
     * @return array Returns formatted log info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function ipLogs(array $filters = [], int $page = 1, int $perPage = 50, bool $raw = false): array
    {
        try {
            $response = $this->api->listIpLogs($filters, $page, $perPage);

            if ($raw) {
                return $response;
            }

            $logs = [];
            foreach ($response['iplogs'] ?? [] as $log) {
                $logs[] = [
                    'id' => (int) $log['iplid'],
                    'ip' => [
                        'id' => (int) $log['ipid'],
                        'address' => $log['ip']
                    ],
                    'vps_id' => (int) $log['vpsid'],
                    'user' => [
                        'id' => (int) $log['uid'],
                        'email' => $log['email']
                    ],
                    'cloud_user' => [
                        'id' => (int) $log['cloud_uid'],
                        'email' => $log['cloud_email']
                    ],
                    'timestamp' => (int) $log['time'],
                    'date' => (int) $log['date']
                ];
            }

            return [
                'logs' => $logs,
                'current_status' => $response['current_status'] ?? null,
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to list IP logs: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Delete all IP logs
     *
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function deleteIpLogs(bool $raw = false): array|bool
    {
        try {
            $response = $this->api->deleteIpLogs();

            if ($raw) {
                return $response;
            }

            // Verify logs were deleted by checking if iplogs array is empty
            if (!isset($response['iplogs']) || !is_array($response['iplogs']) || !empty($response['iplogs'])) {
                throw new VirtualizorApiException(
                    'Failed to delete IP logs: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to delete IP logs: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }
} 