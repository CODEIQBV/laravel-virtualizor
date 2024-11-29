<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

class BackupManager
{
    public function __construct(
        private readonly AdminApi $api
    ) {}

    /**
     * List backup servers
     *
     * @param array{
     *    name?: string,
     *    hostname?: string,
     *    type?: string
     * } $filters Optional filters
     * @param  int  $page  Page number
     * @param  int  $perPage  Records per page
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function list(array $filters = [], int $page = 1, int $perPage = 50, bool $raw = false): array
    {
        try {
            $response = $this->api->listBackupServers($page, $perPage, $filters);

            if ($raw) {
                return $response;
            }

            return array_map(function ($server) {
                return [
                    'id' => (int) $server['bid'],
                    'type' => $server['type'],
                    'name' => $server['name'],
                    'hostname' => $server['hostname'],
                    'username' => $server['username'],
                    'port' => (int) $server['port'],
                    'directory' => $server['dir'],
                ];
            }, $response['backupservers'] ?? []);
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to list backup servers: '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Create a new backup server
     *
     * @param array{
     *    hostname?: string,
     *    name?: string,
     *    type?: string,
     *    username?: string,
     *    password?: string,
     *    port?: int,
     *    ssh_key?: bool,
     *    sshpub_key?: string,
     *    sshpri_key?: string,
     *    gen_key?: bool,
     *    ftps?: bool
     * } $serverData Backup server creation data
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function create(array $serverData, bool $raw = false): array
    {
        // Validate required fields
        if (empty($serverData['hostname'])) {
            throw new VirtualizorApiException('Hostname is required');
        }
        if (empty($serverData['name'])) {
            throw new VirtualizorApiException('Server name is required');
        }
        if (empty($serverData['type'])) {
            throw new VirtualizorApiException('Server type is required (SSH or FTP)');
        }
        if (empty($serverData['username'])) {
            throw new VirtualizorApiException('Username is required');
        }
        if (empty($serverData['port'])) {
            throw new VirtualizorApiException('Port is required');
        }

        // Validate SSH key requirements
        if (! empty($serverData['ssh_key'])) {
            if (empty($serverData['sshpub_key']) && empty($serverData['gen_key'])) {
                throw new VirtualizorApiException('SSH public key is required when using key authentication');
            }
            if (empty($serverData['sshpri_key']) && empty($serverData['gen_key'])) {
                throw new VirtualizorApiException('SSH private key is required when using key authentication');
            }
        }

        try {
            $response = $this->api->addBackupServer($serverData);

            if ($raw) {
                return $response;
            }

            if (! empty($response['done'])) {
                $result = [
                    'success' => true,
                    'id' => (int) $response['done'],
                    'name' => $serverData['name'],
                    'type' => $serverData['type'],
                    'hostname' => $serverData['hostname'],
                ];

                // Include generated keys if available
                if (! empty($response['keys'])) {
                    $result['keys'] = $response['keys'];
                }

                return $result;
            }

            throw new VirtualizorApiException('Failed to create backup server: Unknown error');
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to create backup server: '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Edit a backup server
     *
     * @param array{
     *    name?: string,
     *    hostname?: string,
     *    type?: string,
     *    username?: string,
     *    password?: string,
     *    port?: int,
     *    ssh_key?: bool,
     *    sshpub_key?: string,
     *    sshpri_key?: string,
     *    ftps?: bool
     * } $serverData Backup server update data
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function update(int $serverId, array $serverData, bool $raw = false): array
    {
        // Validate required fields
        if (empty($serverData['hostname'])) {
            throw new VirtualizorApiException('Hostname is required');
        }
        if (empty($serverData['name'])) {
            throw new VirtualizorApiException('Server name is required');
        }
        if (empty($serverData['type'])) {
            throw new VirtualizorApiException('Server type is required (SSH or FTP)');
        }
        if (empty($serverData['username'])) {
            throw new VirtualizorApiException('Username is required');
        }
        if (empty($serverData['port'])) {
            throw new VirtualizorApiException('Port is required');
        }

        // Validate SSH key requirements
        if (! empty($serverData['ssh_key'])) {
            if (empty($serverData['sshpub_key'])) {
                throw new VirtualizorApiException('SSH public key is required when using key authentication');
            }
            if (empty($serverData['sshpri_key'])) {
                throw new VirtualizorApiException('SSH private key is required when using key authentication');
            }
        }

        try {
            $response = $this->api->editBackupServer($serverId, $serverData);

            if ($raw) {
                return $response;
            }

            if (! empty($response['done'])) {
                return [
                    'success' => true,
                    'id' => $serverId,
                    'name' => $serverData['name'],
                    'type' => $serverData['type'],
                    'hostname' => $serverData['hostname'],
                ];
            }

            throw new VirtualizorApiException('Failed to update backup server: Unknown error');
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to update backup server {$serverId}: ".$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Delete one or more backup servers
     *
     * @param  int|array  $serverIds  Single server ID or array of server IDs
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function delete($serverIds, bool $raw = false): array
    {
        try {
            $response = $this->api->deleteBackupServers($serverIds);

            if ($raw) {
                return $response;
            }

            if (! empty($response['done'])) {
                return [
                    'success' => true,
                    'deleted' => $response['done'],
                ];
            }

            throw new VirtualizorApiException('Failed to delete backup server(s): Unknown error');
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to delete backup server(s): '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Test backup server connectivity
     *
     * @param  int  $serverId  Backup server ID to test
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function test(int $serverId, bool $raw = false): array
    {
        try {
            $response = $this->api->testBackupServer($serverId);

            if ($raw) {
                return $response;
            }

            return [
                'success' => $response['test_result'] === 'success',
                'timestamp' => $response['timenow'] ?? null,
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to test backup server {$serverId}: ".$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * List backup plans
     *
     * @param array{
     *    planname?: string
     * } $filters Search filters
     * @param int $page Page number
     * @param int $perPage Records per page
     * @param bool $raw Return raw API response
     * @return array Returns formatted backup plan info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function listPlans(array $filters = [], int $page = 1, int $perPage = 50, bool $raw = false): array
    {
        try {
            $response = $this->api->listBackupPlans($filters, $page, $perPage);

            if ($raw) {
                return $response;
            }

            $plans = [];
            foreach ($response['backup_plans'] ?? [] as $id => $plan) {
                $plans[] = [
                    'id' => (int) $plan['bpid'],
                    'name' => $plan['plan_name'],
                    'is_enabled' => !(bool) $plan['disabled'],
                    'backup_server' => [
                        'id' => (int) $plan['bid'],
                        'name' => $plan['backup_server']
                    ],
                    'schedule' => [
                        'frequency' => $plan['frequency'],
                        'time' => $plan['run_time'],
                        'hourly_frequency' => (int) $plan['hourly_freq'],
                        'day' => (int) $plan['run_day'],
                        'date' => (int) $plan['run_date']
                    ],
                    'settings' => [
                        'rotation' => (int) $plan['rotation'],
                        'backup_limit' => (int) $plan['backup_limit'],
                        'restore_limit' => (int) $plan['restore_limit'],
                        'enable_enduser_backup_servers' => (bool) $plan['enable_enduser_backup_servers'],
                        'nice' => (int) $plan['nice'],
                        'ionice' => [
                            'priority' => (int) $plan['ionice_prio'],
                            'class' => (int) $plan['ionice_class']
                        ],
                        'disable_compression' => (bool) $plan['disable_compression'],
                        'directory' => $plan['dir']
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
                'Failed to list backup plans: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Available backup types
     */
    private const BACKUP_TYPES = ['LOCAL', 'FTP', 'SSH'];

    /**
     * Available backup frequencies
     */
    private const BACKUP_FREQUENCIES = ['hourly', 'daily', 'weekly', 'monthly'];

    /**
     * Available IO classes
     */
    private const IO_CLASSES = [
        1 => 'Real time',
        2 => 'Best Effort',
        3 => 'Idle'
    ];

    /**
     * Create a new backup plan
     *
     * @param array{
     *    disabled?: bool,
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
     *    compression?: bool
     * } $params Backup plan parameters
     * @param bool $raw Return raw API response
     * @return array|int Returns plan ID when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function createPlan(array $params, bool $raw = false): array|int
    {
        try {
            // Validate required fields
            $required = ['plan_name', 'type', 'dir', 'freq', 'hourly_freq', 'hrs', 'min', 
                        'day', 'date', 'rotation', 'backup_limit', 'restore_limit', 'nice', 
                        'ionice_prio', 'ionice_class'];
            foreach ($required as $field) {
                if (!isset($params[$field])) {
                    throw new VirtualizorApiException("{$field} is required");
                }
            }

            // Validate backup type
            if (!in_array($params['type'], self::BACKUP_TYPES)) {
                throw new VirtualizorApiException(
                    'Invalid backup type. Available types: ' . implode(', ', self::BACKUP_TYPES)
                );
            }

            // Validate backup server ID for FTP/SSH
            if (in_array($params['type'], ['FTP', 'SSH']) && empty($params['id'])) {
                throw new VirtualizorApiException('Backup server ID is required for FTP/SSH backup type');
            }

            // Validate frequency
            if (!in_array($params['freq'], self::BACKUP_FREQUENCIES)) {
                throw new VirtualizorApiException(
                    'Invalid frequency. Available frequencies: ' . implode(', ', self::BACKUP_FREQUENCIES)
                );
            }

            // Validate numeric ranges
            $validations = [
                'hourly_freq' => [0, 23],
                'hrs' => [0, 23],
                'min' => [0, 59],
                'day' => [1, 7],
                'date' => [1, 31],
                'rotation' => [0, 10],
                'backup_limit' => [-1, 10],
                'restore_limit' => [-1, 10],
                'nice' => [-20, 19],
                'ionice_prio' => [0, 7]
            ];

            foreach ($validations as $field => [$min, $max]) {
                if ($params[$field] < $min || $params[$field] > $max) {
                    throw new VirtualizorApiException("{$field} must be between {$min} and {$max}");
                }
            }

            // Validate IO class
            if (!array_key_exists($params['ionice_class'], self::IO_CLASSES)) {
                throw new VirtualizorApiException(
                    'Invalid IO class. Available classes: ' . implode(', ', self::IO_CLASSES)
                );
            }

            // Convert boolean values to integers
            if (isset($params['disabled'])) {
                $params['disabled'] = $params['disabled'] ? 1 : 0;
            }

            if (isset($params['compression'])) {
                $params['compression'] = $params['compression'] ? 1 : 0;
            }

            $response = $this->api->addBackupPlan($params);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to create backup plan: Operation unsuccessful'
                );
            }

            return (int) $response['done'];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to create backup plan: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Edit a backup plan
     *
     * @param int $planId Backup plan ID to edit
     * @param array{
     *    disabled?: bool,
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
     *    compression?: bool
     * } $params Backup plan parameters
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function editPlan(int $planId, array $params, bool $raw = false): array|bool
    {
        try {
            // Validate required fields
            $required = ['plan_name', 'type', 'dir', 'freq', 'hourly_freq', 'hrs', 'min', 
                        'day', 'date', 'rotation', 'backup_limit', 'restore_limit', 'nice', 
                        'ionice_prio', 'ionice_class'];
            foreach ($required as $field) {
                if (!isset($params[$field])) {
                    throw new VirtualizorApiException("{$field} is required");
                }
            }

            // Validate backup type
            if (!in_array($params['type'], self::BACKUP_TYPES)) {
                throw new VirtualizorApiException(
                    'Invalid backup type. Available types: ' . implode(', ', self::BACKUP_TYPES)
                );
            }

            // Validate backup server ID for FTP/SSH
            if (in_array($params['type'], ['FTP', 'SSH']) && empty($params['id'])) {
                throw new VirtualizorApiException('Backup server ID is required for FTP/SSH backup type');
            }

            // Validate frequency
            if (!in_array($params['freq'], self::BACKUP_FREQUENCIES)) {
                throw new VirtualizorApiException(
                    'Invalid frequency. Available frequencies: ' . implode(', ', self::BACKUP_FREQUENCIES)
                );
            }

            // Validate numeric ranges
            $validations = [
                'hourly_freq' => [0, 23],
                'hrs' => [0, 23],
                'min' => [0, 59],
                'day' => [1, 7],
                'date' => [1, 31],
                'rotation' => [0, 10],
                'backup_limit' => [-1, 10],
                'restore_limit' => [-1, 10],
                'nice' => [-20, 19],
                'ionice_prio' => [0, 7]
            ];

            foreach ($validations as $field => [$min, $max]) {
                if ($params[$field] < $min || $params[$field] > $max) {
                    throw new VirtualizorApiException("{$field} must be between {$min} and {$max}");
                }
            }

            // Validate IO class
            if (!array_key_exists($params['ionice_class'], self::IO_CLASSES)) {
                throw new VirtualizorApiException(
                    'Invalid IO class. Available classes: ' . implode(', ', self::IO_CLASSES)
                );
            }

            // Convert boolean values to integers
            if (isset($params['disabled'])) {
                $params['disabled'] = $params['disabled'] ? 1 : 0;
            }

            if (isset($params['compression'])) {
                $params['compression'] = $params['compression'] ? 1 : 0;
            }

            $response = $this->api->editBackupPlan($planId, $params);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to edit backup plan: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to edit backup plan {$planId}: " . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Delete backup plan(s)
     *
     * @param int|array $planIds Single plan ID or array of plan IDs
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function deletePlan(int|array $planIds, bool $raw = false): array|bool
    {
        try {
            $response = $this->api->deleteBackupPlans($planIds);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to delete backup plan: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            $ids = is_array($planIds) ? implode(', ', $planIds) : $planIds;
            throw new VirtualizorApiException(
                "Failed to delete backup plan(s) {$ids}: " . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Configure database backups
     *
     * @param array{
     *    enabled: bool,
     *    type: string,
     *    cron: string,
     *    email?: string,
     *    server_id?: int,
     *    server_dir?: string
     * } $params Backup configuration parameters
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function configureDatabaseBackups(array $params, bool $raw = false): array|bool
    {
        try {
            // Validate required fields
            if (!isset($params['enabled'])) {
                throw new VirtualizorApiException('enabled parameter is required');
            }

            if (!isset($params['type'])) {
                throw new VirtualizorApiException('type parameter is required');
            }

            if (!isset($params['cron'])) {
                throw new VirtualizorApiException('cron parameter is required');
            }

            // Validate backup type
            if (!in_array($params['type'], self::BACKUP_TYPES)) {
                throw new VirtualizorApiException(
                    'Invalid backup type. Available types: ' . implode(', ', self::BACKUP_TYPES)
                );
            }

            // Validate type-specific requirements
            if ($params['type'] === 'EMAIL' && empty($params['email'])) {
                throw new VirtualizorApiException('email is required for EMAIL backup type');
            }

            if (in_array($params['type'], ['SSH', 'FTP'])) {
                if (empty($params['server_id'])) {
                    throw new VirtualizorApiException('server_id is required for SSH/FTP backup type');
                }
                if (empty($params['server_dir'])) {
                    throw new VirtualizorApiException('server_dir is required for SSH/FTP backup type');
                }
            }

            // Validate cron format
            if (!preg_match('/^(\*|[0-9,\-\/]+)\s+(\*|[0-9,\-\/]+)\s+(\*|[0-9,\-\/]+)\s+(\*|[0-9,\-\/]+)\s+(\*|[0-9,\-\/]+)$/', $params['cron'])) {
                throw new VirtualizorApiException('Invalid cron format. Must be in standard cron format (e.g. "0 0 * * *")');
            }

            $requestParams = [
                'databasebackups' => $params['enabled'] ? 1 : 0,
                'type' => $params['type'],
                'dbbackup_cron' => $params['cron']
            ];

            if ($params['type'] === 'EMAIL') {
                $requestParams['email'] = $params['email'];
            } else {
                $requestParams['dbbackup_server'] = $params['server_id'];
                $requestParams['dbbackup_server_dir'] = $params['server_dir'];
            }

            $response = $this->api->configureDatabaseBackups($requestParams);

            if ($raw) {
                return $response;
            }

            if (empty($response['done']['cron_set'])) {
                throw new VirtualizorApiException(
                    'Failed to configure database backups: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to configure database backups: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Disable database backups
     *
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function disableDatabaseBackups(bool $raw = false): array|bool
    {
        try {
            $response = $this->api->disableDatabaseBackups();

            if ($raw) {
                return $response;
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to disable database backups: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * List database backups
     *
     * @param bool $raw Return raw API response
     * @return array Returns formatted backup info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function listDatabaseBackups(bool $raw = false): array
    {
        try {
            $response = $this->api->listDatabaseBackups();

            if ($raw) {
                return $response;
            }

            $backups = [];
            foreach ($response['filename'] ?? [] as $id => $filename) {
                $backups[] = [
                    'id' => (int) $id,
                    'filename' => $filename,
                    'timestamp' => $this->parseBackupTimestamp($filename)
                ];
            }

            return [
                'backups' => $backups,
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to list database backups: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Parse backup timestamp from filename
     * Format: virtualizor-YYYY-MM-DD_HH.MM.SS.sql.gz
     *
     * @param string $filename Backup filename
     * @return int|null Unix timestamp or null if invalid format
     */
    private function parseBackupTimestamp(string $filename): ?int
    {
        if (preg_match('/virtualizor-(\d{4})-(\d{2})-(\d{2})_(\d{2})\.(\d{2})\.(\d{2})/', $filename, $matches)) {
            return mktime(
                (int) $matches[4], // hour
                (int) $matches[5], // minute
                (int) $matches[6], // second
                (int) $matches[2], // month
                (int) $matches[3], // day
                (int) $matches[1]  // year
            );
        }
        return null;
    }

    /**
     * Delete database backup(s)
     *
     * @param string|array $backupIds Single backup ID or array of backup IDs
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function deleteDatabaseBackup(string|array $backupIds, bool $raw = false): array|bool
    {
        try {
            // Convert array to comma-separated string if needed
            $ids = is_array($backupIds) ? implode(',', $backupIds) : $backupIds;

            $response = $this->api->deleteDatabaseBackup($ids);

            if ($raw) {
                return $response;
            }

            if (empty($response['done']['delete'])) {
                throw new VirtualizorApiException(
                    'Failed to delete database backup: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            $ids = is_array($backupIds) ? implode(', ', $backupIds) : $backupIds;
            throw new VirtualizorApiException(
                "Failed to delete database backup(s) {$ids}: " . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Create database backup
     *
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function createDatabaseBackup(bool $raw = false): array|bool
    {
        try {
            $response = $this->api->createDatabaseBackup();

            if ($raw) {
                return $response;
            }

            if (empty($response['done']['succ'])) {
                throw new VirtualizorApiException(
                    'Failed to create database backup: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to create database backup: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Create VPS backup
     *
     * @param int $planId Backup plan ID
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function createVpsBackup(int $planId, bool $raw = false): array|bool
    {
        try {
            $response = $this->api->createVpsBackup($planId);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to create VPS backup: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to create VPS backup for plan {$planId}: " . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Get VPS backup details
     *
     * @param int $vpsId VPS ID to get backup details for
     * @param string|null $date Specific date to get backups for (format: YYYYMMDD)
     * @param bool $raw Return raw API response
     * @return array Returns formatted backup info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function getVpsBackupDetails(int $vpsId, ?string $date = null, bool $raw = false): array
    {
        try {
            $params = ['vpsid' => $vpsId];
            if ($date !== null) {
                // Validate date format
                if (!preg_match('/^\d{8}$/', $date)) {
                    throw new VirtualizorApiException('Date must be in YYYYMMDD format (e.g. 20240101)');
                }
                $params['date'] = $date;
            }

            $response = $this->api->getVpsBackupDetails($params);

            if ($raw) {
                return $response;
            }

            $backups = [];
            foreach ($response['backup_list'] ?? [] as $backupDate => $files) {
                $backups[$backupDate] = array_map(function($file) {
                    return [
                        'path' => $file['abs_path'],
                        'size' => [
                            'bytes' => (int) $file['size'],
                            'mb' => round((int) $file['size'] / 1024 / 1024, 2),
                            'gb' => round((int) $file['size'] / 1024 / 1024 / 1024, 2)
                        ]
                    ];
                }, $files);
            }

            return [
                'backups' => $backups,
                'directories' => $response['directories'] ?? [],
                'server' => [
                    'id' => (int) ($response['vps_backup_server'] ?? 0),
                    'directory' => $response['vps_backup_dir'] ?? null
                ],
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to get backup details for VPS {$vpsId}: " . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Restore VPS backup
     *
     * @param array{
     *    vpsid: int,
     *    dir: string,
     *    date: string,
     *    file: string,
     *    bid?: int,
     *    newvps?: bool,
     *    newserid?: int
     * } $params Restore parameters
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function restoreVpsBackup(array $params, bool $raw = false): array|bool
    {
        try {
            // Validate required fields
            $required = ['vpsid', 'dir', 'date', 'file'];
            foreach ($required as $field) {
                if (!isset($params[$field])) {
                    throw new VirtualizorApiException("{$field} is required");
                }
            }

            // Validate date format (YYYYMMDD)
            if (!preg_match('/^\d{8}$/', $params['date'])) {
                throw new VirtualizorApiException('date must be in YYYYMMDD format (e.g. 20240101)');
            }

            // Convert boolean to integer
            if (isset($params['newvps'])) {
                $params['newvps'] = $params['newvps'] ? 1 : 0;
            }

            $response = $this->api->restoreVpsBackup($params);

            if ($raw) {
                return $response;
            }

            if (empty($response['restore_done'])) {
                throw new VirtualizorApiException(
                    'Failed to restore VPS backup: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to restore backup for VPS {$params['vpsid']}: " . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Delete VPS backup
     *
     * @param array{
     *    file: string,
     *    dir: string,
     *    date: string,
     *    bid?: int
     * } $params Delete parameters
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function deleteVpsBackup(array $params, bool $raw = false): array|bool
    {
        try {
            // Validate required fields
            $required = ['file', 'dir', 'date'];
            foreach ($required as $field) {
                if (!isset($params[$field])) {
                    throw new VirtualizorApiException("{$field} is required");
                }
            }

            // Validate date format (YYYYMMDD)
            if (!preg_match('/^\d{8}$/', $params['date'])) {
                throw new VirtualizorApiException('date must be in YYYYMMDD format (e.g. 20240101)');
            }

            $response = $this->api->deleteVpsBackup($params);

            if ($raw) {
                return $response;
            }

            if (empty($response['delete_done'])) {
                throw new VirtualizorApiException(
                    'Failed to delete VPS backup: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to delete VPS backup: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Create single VPS backup
     *
     * @param int $vpsId VPS ID to backup
     * @param bool $raw Return raw API response
     * @return array|int Returns task ID when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function createSingleVpsBackup(int $vpsId, bool $raw = false): array|int
    {
        try {
            $response = $this->api->createSingleVpsBackup($vpsId);

            if ($raw) {
                return $response;
            }

            if (empty($response['done']['msg']) || 
                !str_contains($response['done']['msg'], 'backup was started successfully')) {
                throw new VirtualizorApiException(
                    'Failed to create VPS backup: Operation unsuccessful'
                );
            }

            return (int) $response['actid'];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to create backup for VPS {$vpsId}: " . $e->getMessage(),
                $e->getContext()
            );
        }
    }
}
