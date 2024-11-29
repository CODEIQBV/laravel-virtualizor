<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

class ServerGroupManager
{
    public function __construct(
        private readonly AdminApi $api
    ) {}

    /**
     * List all server groups
     *
     * @param array{
     *    sg_name?: string
     * } $filters Optional filters
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function list(array $filters = [], bool $raw = false): array
    {
        try {
            $response = $this->api->listServerGroups($filters);

            if ($raw) {
                return $response;
            }

            return array_map(function ($group) {
                return [
                    'id' => (int) $group['sgid'],
                    'name' => $group['sg_name'],
                    'reseller_name' => $group['sg_reseller_name'],
                    'description' => $group['sg_desc'],
                    'ha_enabled' => $group['sg_ha'] ?? false,
                    'total_servers' => $group['totalservers'] ?? 0,
                    'servers' => $group['servers'] ?? [],
                ];
            }, $response['servergroups'] ?? []);
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to list server groups: '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Create a new server group
     *
     * @param array{
     *    sg_name: string,
     *    sg_reseller_name?: string,
     *    sg_desc?: string,
     *    sg_select?: int
     * } $groupData Server group creation data
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function create(array $groupData, bool $raw = false): array
    {
        // Validate required fields
        if (! isset($groupData['sg_name'])) {
            throw new VirtualizorApiException('Server group name is required');
        }

        try {
            $response = $this->api->addServerGroup($groupData);

            if ($raw) {
                return $response;
            }

            if (! empty($response['done'])) {
                return [
                    'success' => true,
                    'id' => (int) $response['done'],
                    'name' => $groupData['sg_name'],
                    'reseller_name' => $groupData['sg_reseller_name'] ?? null,
                    'description' => $groupData['sg_desc'] ?? null,
                    'selection_mode' => $groupData['sg_select'] ?? 0,
                ];
            }

            throw new VirtualizorApiException('Failed to create server group: Unknown error');
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to create server group: '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Edit a server group
     *
     * @param array{
     *    sg_name: string,
     *    sg_reseller_name?: string,
     *    sg_desc?: string,
     *    sg_select?: int
     * } $groupData Server group update data
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function update(int $groupId, array $groupData, bool $raw = false): array
    {
        // Validate required fields
        if (! isset($groupData['sg_name'])) {
            throw new VirtualizorApiException('Server group name is required');
        }

        try {
            $response = $this->api->editServerGroup($groupId, $groupData);

            if ($raw) {
                return $response;
            }

            if (! empty($response['done'])) {
                return [
                    'success' => true,
                    'id' => $groupId,
                    'group' => $response['servergroup'] ?? [],
                ];
            }

            throw new VirtualizorApiException('Failed to update server group: Unknown error');
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to update server group {$groupId}: ".$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Delete one or more server groups
     *
     * @param  int|array  $groupIds  Single group ID or array of group IDs
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function delete($groupIds, bool $raw = false): array
    {
        try {
            $response = $this->api->deleteServerGroups($groupIds);

            if ($raw) {
                return $response;
            }

            return [
                'success' => true,
                'deleted' => $response['done'] ?? [],
                'remaining_groups' => $response['servergroups'] ?? [],
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to delete server group(s): '.$e->getMessage(),
                $e->getContext()
            );
        }
    }
}
