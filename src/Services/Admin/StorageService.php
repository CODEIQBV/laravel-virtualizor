<?php

namespace App\Services\Admin;

use App\Api\VirtualizorApi;
use App\Exceptions\VirtualizorApiException;

class StorageService
{
    private VirtualizorApi $api;

    public function __construct(VirtualizorApi $api)
    {
        $this->api = $api;
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
} 