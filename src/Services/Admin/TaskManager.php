<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

class TaskManager
{
    /**
     * Available task actions
     */
    private const TASK_ACTIONS = [
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

    /**
     * Available task statuses
     */
    private const TASK_STATUSES = [
        'IN_PROGRESS' => 1,
        'COMPLETED' => 2,
        'UPDATED' => 3,
        'ERRORED' => -1
    ];

    public function __construct(
        private readonly AdminApi $api
    ) {}

    /**
     * Show tasks
     *
     * @param bool $raw Return raw API response
     * @return array Returns formatted task info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function show(bool $raw = false): array
    {
        try {
            $response = $this->api->showTasks();

            if ($raw) {
                return $response;
            }

            $tasks = [];
            foreach ($response['tasks'] ?? [] as $id => $task) {
                $tasks[] = [
                    'id' => (int) $task['actid'],
                    'slave_id' => (int) $task['slaveactid'],
                    'user' => [
                        'id' => (int) $task['uid'],
                        'email' => $task['email'],
                        'ip' => $task['ip']
                    ],
                    'vps' => [
                        'id' => (int) $task['vpsid']
                    ],
                    'server' => [
                        'id' => (int) $task['serid'],
                        'name' => $task['server_name']
                    ],
                    'action' => [
                        'type' => $task['action'],
                        'description' => self::TASK_ACTIONS[$task['action']] ?? $task['action'],
                        'text' => $task['action_txt']
                    ],
                    'status' => [
                        'code' => (int) $task['status'],
                        'text' => $task['status_txt'],
                        'progress' => (int) $task['progress']
                    ],
                    'timing' => [
                        'started' => $task['started'],
                        'updated' => $task['updated'],
                        'ended' => $task['ended'],
                        'timestamp' => (int) $task['time']
                    ],
                    'process_id' => (int) $task['proc_id'],
                    'is_internal' => (bool) $task['internal'],
                    'data' => $this->parseTaskData($task['data'])
                ];
            }

            return [
                'tasks' => $tasks,
                'logs' => $response['logs_data'] ?? null,
                'logs_info' => $response['l_common_logs'] ?? null,
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to show tasks: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Parse task data
     *
     * @param string|null $data Serialized task data
     * @return array|null Parsed task data
     */
    private function parseTaskData(?string $data): ?array
    {
        if (empty($data)) {
            return null;
        }

        try {
            $unserialized = @unserialize($data);
            return $unserialized !== false ? $unserialized : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Show task logs
     *
     * @param int $taskId Task ID to show logs for
     * @param bool $raw Return raw API response
     * @return array Returns formatted log info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function showLogs(int $taskId, bool $raw = false): array
    {
        try {
            $response = $this->api->showTaskLogs($taskId);

            if ($raw) {
                return $response;
            }

            return [
                'logs' => array_map(function($log) {
                    return [
                        'category' => $log['category'] ?? 'unknown',
                        'message' => $log['message'] ?? ''
                    ];
                }, $response['logs_data']['logs'] ?? []),
                'info' => $response['l_common_logs'] ?? null,
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to show logs for task {$taskId}: " . $e->getMessage(),
                $e->getContext()
            );
        }
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
     * @param bool $raw Return raw API response
     * @return array Returns formatted task info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function search(array $filters = [], int $page = 1, int $perPage = 50, bool $raw = false): array
    {
        try {
            // Validate status if provided
            if (isset($filters['status']) && !in_array($filters['status'], self::TASK_STATUSES)) {
                throw new VirtualizorApiException(
                    'Invalid status. Available statuses: IN_PROGRESS (1), COMPLETED (2), UPDATED (3), ERRORED (-1)'
                );
            }

            // Validate action if provided
            if (isset($filters['action']) && !array_key_exists($filters['action'], self::TASK_ACTIONS)) {
                throw new VirtualizorApiException(
                    'Invalid action. Available actions: ' . implode(', ', array_keys(self::TASK_ACTIONS))
                );
            }

            // Validate order if provided
            if (isset($filters['order']) && !in_array(strtoupper($filters['order']), ['ASC', 'DESC'])) {
                throw new VirtualizorApiException('Invalid order. Available orders: ASC, DESC');
            }

            $response = $this->api->searchTasks($filters, $page, $perPage);

            if ($raw) {
                return $response;
            }

            $tasks = [];
            foreach ($response['tasks'] ?? [] as $id => $task) {
                $tasks[] = [
                    'id' => (int) $task['actid'],
                    'slave_id' => (int) $task['slaveactid'],
                    'user' => [
                        'id' => (int) $task['uid'],
                        'email' => $task['email'],
                        'ip' => $task['ip']
                    ],
                    'vps' => [
                        'id' => (int) $task['vpsid']
                    ],
                    'server' => [
                        'id' => (int) $task['serid'],
                        'name' => $task['server_name']
                    ],
                    'action' => [
                        'type' => $task['action'],
                        'description' => self::TASK_ACTIONS[$task['action']] ?? $task['action'],
                        'text' => $task['action_txt']
                    ],
                    'status' => [
                        'code' => (int) $task['status'],
                        'text' => $task['status_txt'],
                        'progress' => (int) $task['progress']
                    ],
                    'timing' => [
                        'started' => $task['started'],
                        'updated' => $task['updated'],
                        'ended' => $task['ended'],
                        'timestamp' => (int) $task['time']
                    ],
                    'process_id' => (int) $task['proc_id'],
                    'is_internal' => (bool) $task['internal'],
                    'data' => $this->parseTaskData($task['data'])
                ];
            }

            return [
                'tasks' => $tasks,
                'logs' => $response['logs_data'] ?? null,
                'logs_info' => $response['l_common_logs'] ?? null,
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to search tasks: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }
} 