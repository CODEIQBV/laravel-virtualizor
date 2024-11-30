<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

class StorageManager
{
    public function __construct(
        private readonly AdminApi $api
    ) {}

    /**
     * List storage information
     *
     * @param array{
     *    name?: string,
     *    path?: string
     * } $filters Search filters
     * @param int $page Page number
     * @param int $perPage Records per page
     * @param bool $raw Return raw API response
     * @return array Returns formatted storage info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function list(array $filters = [], int $page = 1, int $perPage = 50, bool $raw = false): array
    {
        try {
            $response = $this->api->listStorage($filters, $page, $perPage);

            if ($raw) {
                return $response;
            }

            $storages = [];
            foreach ($response['storage'] ?? [] as $storage) {
                $storages[] = [
                    'id' => (int) $storage['stid'],
                    'uuid' => $storage['st_uuid'],
                    'name' => $storage['name'],
                    'path' => $storage['path'],
                    'type' => $storage['type'],
                    'format' => $storage['format'],
                    'size' => [
                        'total' => (float) $storage['size'],
                        'free' => (float) $storage['free'],
                        'used' => (float) $storage['size'] - (float) $storage['free']
                    ],
                    'settings' => [
                        'oversell' => (int) $storage['oversell'],
                        'alert_threshold' => (float) $storage['alert_threshold'],
                        'is_primary' => (bool) $storage['primary_storage'],
                        'last_alert' => (int) $storage['last_alert']
                    ]
                ];
            }

            return [
                'storages' => $storages,
                'server_mappings' => $response['storage_servers'] ?? [],
                'timestamp' => $response['timenow'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to list storage: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Edit storage configuration
     *
     * @param int $storageId Storage ID to edit
     * @param array{
     *    name: string,
     *    oversell?: int,
     *    alert_threshold?: int,
     *    primary_storage?: bool
     * } $params Storage parameters
     * @param bool $raw Return raw API response
     * @return array|bool Returns true/false when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function edit(int $storageId, array $params, bool $raw = false): array|bool
    {
        try {
            if (!array_key_exists('name', $params)) {
                throw new VirtualizorApiException('Storage name is required');
            }

            // Convert boolean to integer for primary_storage
            if (array_key_exists('primary_storage', $params)) {
                $params['primary_storage'] = $params['primary_storage'] ? 1 : 0;
            }

            $response = $this->api->editStorage($storageId, $params);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to edit storage: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to edit storage {$storageId}: " . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Delete storage(s)
     *
     * @param int|array $storageIds Single storage ID or array of storage IDs
     * @param bool $raw Return raw API response
     * @return array Returns deleted storage info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function delete(int|array $storageIds, bool $raw = false): array
    {
        try {
            $response = $this->api->deleteStorage($storageIds);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to delete storage: Operation unsuccessful'
                );
            }

            // Format the deleted storage information
            $deletedStorages = [];
            foreach ($response['done'] as $id => $storage) {
                $deletedStorages[$id] = [
                    'id' => (int) $storage['stid'],
                    'uuid' => $storage['st_uuid'],
                    'name' => $storage['name'],
                    'path' => $storage['path'],
                    'type' => $storage['type'],
                    'format' => $storage['format'],
                    'size' => [
                        'total' => (float) $storage['size'],
                        'free' => (float) $storage['free']
                    ],
                    'settings' => [
                        'oversell' => (int) $storage['oversell'],
                        'alert_threshold' => (float) $storage['alert_threshold'],
                        'is_primary' => (bool) $storage['primary_storage'],
                        'last_alert' => (int) $storage['last_alert']
                    ]
                ];
            }

            return [
                'deleted_storages' => $deletedStorages,
                'server_mappings' => $response['storage_servers'] ?? [],
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            $ids = is_array($storageIds) ? implode(', ', $storageIds) : $storageIds;
            throw new VirtualizorApiException(
                "Failed to delete storage(s) {$ids}: " . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Available storage types and their paths
     */
    private const STORAGE_TYPES = [
        'LVM' => '/dev/VG_NAME',                         // Volume Group path
        'File' => '/FileStorage',                        // Absolute path
        'OpenVZ' => '/vz',                               // Absolute path
        'Thin LVM' => '/dev/VGName/THINPOOLNAME',        // Thin Volume Group path
        'ZFS Pool' => '/dev/zvol/YOUR_POOLNAME',         // ZFS Volume Group path
        'ZFS Thin' => '/dev/zvol/YOUR_POOLNAME',         // ZFS Volume Group path
        'ZFS Compressed' => '/dev/zvol/YOUR_POOLNAME',   // ZFS Volume Group path
        'ZFS Thin Compressed' => '/dev/zvol/YOUR_POOLNAME', // ZFS Volume Group path
        'Ceph Block Device' => '/dev/rbd/CEPH_POOLNAME', // Ceph Block Device path
        'Lightbits storage' => '/dev/disk/by-id/uuid'    // Lightbits Block Device path
    ];

    /**
     * Available storage formats
     */
    private const STORAGE_FORMATS = ['raw', 'vhd', 'qcow2'];

    /**
     * Create new storage
     *
     * @param array{
     *    name: string,
     *    path: string,
     *    type: string,
     *    serid: int|array,
     *    format: string,
     *    primary_storage?: bool,
     *    oversell?: int,
     *    alert_threshold?: int,
     *    lightbit_project?: string
     * } $params Storage parameters
     * @param bool $raw Return raw API response
     * @return array|int Returns storage ID when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function create(array $params, bool $raw = false): array|int
    {
        try {
            // Validate required fields
            $required = ['name', 'path', 'type', 'serid', 'format'];
            $missing = [];
            
            foreach ($required as $field) {
                if (!array_key_exists($field, $params)) {
                    $missing[] = $field;
                }
            }
            
            if (count($missing) > 0) {
                throw new VirtualizorApiException(implode(', ', $missing) . ' are required');
            }

            // Validate storage type
            if (!array_key_exists($params['type'], self::STORAGE_TYPES)) {
                throw new VirtualizorApiException(
                    "Invalid storage type. Available types: " . implode(', ', array_keys(self::STORAGE_TYPES))
                );
            }

            // Validate format
            if (!in_array($params['format'], self::STORAGE_FORMATS)) {
                throw new VirtualizorApiException(
                    "Invalid format. Available formats: " . implode(', ', self::STORAGE_FORMATS)
                );
            }

            // Convert boolean to integer for primary_storage
            if (isset($params['primary_storage'])) {
                $params['primary_storage'] = $params['primary_storage'] ? 1 : 0;
            }

            // Validate oversell and alert_threshold if provided
            if (isset($params['oversell']) && (!is_int($params['oversell']) || $params['oversell'] < 0)) {
                throw new VirtualizorApiException('oversell must be a positive integer');
            }

            if (isset($params['alert_threshold']) && (!is_int($params['alert_threshold']) || $params['alert_threshold'] < 0 || $params['alert_threshold'] > 100)) {
                throw new VirtualizorApiException('alert_threshold must be between 0 and 100');
            }

            $response = $this->api->addStorage($params);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to create storage: Operation unsuccessful'
                );
            }

            return (int) $response['done'];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to create storage: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Delete orphaned disks
     *
     * @param string|array $diskPaths Single disk path or array of disk paths to delete
     * @param bool $raw Return raw API response
     * @return array Returns formatted response when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function deleteOrphanedDisks(string|array $diskPaths, bool $raw = false): array
    {
        try {
            // Convert array of paths to comma-separated string if needed
            $paths = is_array($diskPaths) ? implode(',', $diskPaths) : $diskPaths;

            $response = $this->api->orphaneddisks([
                'delete' => $paths
            ]);

            if ($raw) {
                return $response;
            }

            // Return formatted response
            return [
                'success' => ($response['done'] ?? '') === 'success',
                'orphaned_disks' => $response['orphaned_disks'] ?? []
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to delete orphaned disks: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * List orphaned disks
     *
     * @param bool $raw Return raw API response
     * @return array Returns formatted response when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function listOrphanedDisks(bool $raw = false): array
    {
        try {
            $response = $this->api->orphaneddisks([]);

            if ($raw) {
                return $response;
            }

            return $response['orphaned_disks'] ?? [];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to list orphaned disks: ' . $e->getMessage(),
                $e->getContext()
            );
        }
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
     * @param bool $raw Return raw API response
     * @return array Returns formatted volume info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function listVolumes(array $filters = [], int $page = 1, int $perPage = 50, bool $raw = false): array
    {
        try {
            $response = $this->api->listVolumes($filters, $page, $perPage);

            if ($raw) {
                return $response;
            }

            return [
                'volumes' => $response['storage_disk'] ?? [],
                'users' => $response['users'] ?? [],
                'format_types' => $response['format_type'] ?? [],
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to list volumes: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Create a new volume
     *
     * @param array{
     *    disk_name: string,
     *    vpsid: int,
     *    newserid: int,
     *    size: float,
     *    format_type: string,
     *    attach?: bool,
     *    mnt_point?: string,
     *    st_uuid?: string
     * } $params Volume parameters
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function createVolume(array $params, bool $raw = false): array|bool
    {
        try {
            // Validate required fields
            $required = ['disk_name', 'vpsid', 'newserid', 'size', 'format_type'];
            foreach ($required as $field) {
                if (empty($params[$field])) {
                    throw new VirtualizorApiException("{$field} is required");
                }
            }

            // Validate format type
            if (!in_array($params['format_type'], ['ext4', 'xfs'])) {
                throw new VirtualizorApiException("format_type must be either 'ext4' or 'xfs'");
            }

            // Convert boolean attach to integer
            if (isset($params['attach'])) {
                $params['attach'] = $params['attach'] ? 1 : 0;
            }

            $response = $this->api->addVolume($params);

            if ($raw) {
                return $response;
            }

            if (empty($response['done']['voladd_done'])) {
                throw new VirtualizorApiException(
                    'Failed to create volume: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to create volume: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Volume actions
     */
    private const VOLUME_ACTIONS = [
        'ATTACH' => 1,
        'DETACH' => 2,
        'RESIZE' => 3
    ];

    /**
     * Attach a volume to a VPS
     *
     * @param int $volumeId Volume ID to attach
     * @param int $serverId Server ID where volume is located
     * @param int $vpsId VPS ID to attach volume to
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function attachVolume(int $volumeId, int $serverId, int $vpsId, bool $raw = false): array|bool
    {
        return $this->editVolume([
            'disk_did_action' => $volumeId,
            'e_serid' => $serverId,
            'e_vpsid' => $vpsId,
            'e_todo' => self::VOLUME_ACTIONS['ATTACH']
        ], $raw);
    }

    /**
     * Detach a volume from a VPS
     *
     * @param int $volumeId Volume ID to detach
     * @param int $serverId Server ID where volume is located
     * @param int $vpsId VPS ID to detach volume from
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function detachVolume(int $volumeId, int $serverId, int $vpsId, bool $raw = false): array|bool
    {
        return $this->editVolume([
            'disk_did_action' => $volumeId,
            'e_serid' => $serverId,
            'e_vpsid' => $vpsId,
            'e_todo' => self::VOLUME_ACTIONS['DETACH']
        ], $raw);
    }

    /**
     * Resize a volume
     *
     * @param int $volumeId Volume ID to resize
     * @param int $serverId Server ID where volume is located
     * @param int $vpsId VPS ID the volume belongs to
     * @param float $newSize New size in GB
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function resizeVolume(int $volumeId, int $serverId, int $vpsId, float $newSize, bool $raw = false): array|bool
    {
        return $this->editVolume([
            'disk_did_action' => $volumeId,
            'e_serid' => $serverId,
            'e_vpsid' => $vpsId,
            'e_todo' => self::VOLUME_ACTIONS['RESIZE'],
            'e_disk_size' => $newSize
        ], $raw);
    }

    /**
     * Edit volume
     *
     * @param array{
     *    disk_did_action: int,
     *    e_serid: int,
     *    e_vpsid: int,
     *    e_todo: int,
     *    e_disk_size?: float
     * } $params Volume edit parameters
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    private function editVolume(array $params, bool $raw = false): array|bool
    {
        try {
            // Validate required fields
            $required = ['disk_did_action', 'e_serid', 'e_vpsid', 'e_todo'];
            foreach ($required as $field) {
                if (!isset($params[$field])) {
                    throw new VirtualizorApiException("{$field} is required");
                }
            }

            // Validate action
            if (!in_array($params['e_todo'], self::VOLUME_ACTIONS)) {
                throw new VirtualizorApiException(
                    'Invalid action. Available actions: ' . implode(', ', array_keys(self::VOLUME_ACTIONS))
                );
            }

            // Validate size if resizing
            if ($params['e_todo'] === self::VOLUME_ACTIONS['RESIZE'] && empty($params['e_disk_size'])) {
                throw new VirtualizorApiException('e_disk_size is required for resize action');
            }

            $response = $this->api->editVolume($params);

            if ($raw) {
                return $response;
            }

            if (empty($response['done']['action_done'])) {
                throw new VirtualizorApiException(
                    'Failed to edit volume: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to edit volume: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Delete volume(s)
     *
     * @param int|array $volumeIds Single volume ID or array of volume IDs
     * @param bool $raw Return raw API response
     * @return array Returns deleted volume info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function deleteVolume(int|array $volumeIds, bool $raw = false): array
    {
        try {
            $response = $this->api->deleteVolumes($volumeIds);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to delete volume: Operation unsuccessful'
                );
            }

            // Format the deleted volume information
            $deletedVolumes = [];
            foreach ($response['done'] as $id => $volume) {
                $deletedVolumes[$id] = [
                    'id' => (int) $volume['did'],
                    'uuid' => $volume['disk_uuid'],
                    'storage_uuid' => $volume['st_uuid'],
                    'vps_uuid' => $volume['vps_uuid'],
                    'path' => $volume['path'],
                    'name' => $volume['disk_name'],
                    'mount_point' => $volume['mnt_point'],
                    'size' => [
                        'value' => (float) $volume['size'],
                        'unit' => $volume['size_unit']
                    ],
                    'type' => $volume['type'],
                    'format' => $volume['format'],
                    'is_primary' => (bool) $volume['primary'],
                    'is_rescue' => (bool) $volume['rescue'],
                    'bus_driver' => [
                        'type' => $volume['bus_driver'],
                        'number' => (int) $volume['bus_driver_num']
                    ],
                    'server' => [
                        'id' => (int) $volume['serid'],
                        'group_id' => (int) $volume['sgid']
                    ],
                    'storage_id' => (int) $volume['stid'],
                    'user_id' => (int) $volume['user_uid']
                ];
            }

            return [
                'deleted_volumes' => $deletedVolumes,
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            $ids = is_array($volumeIds) ? implode(', ', $volumeIds) : $volumeIds;
            throw new VirtualizorApiException(
                "Failed to delete volume(s) {$ids}: " . $e->getMessage(),
                $e->getContext()
            );
        }
    }
}
