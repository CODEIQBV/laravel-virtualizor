<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

class ServerManager
{
    public function __construct(
        private readonly AdminApi $api
    ) {}

    /**
     * List all servers
     *
     * @param array{
     *    servername?: string,
     *    serverip?: string,
     *    ptype?: string
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
            $response = $this->api->listServers($page, $perPage, $filters);

            if ($raw) {
                return $response;
            }

            return $response['servs'] ?? [];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to list servers: '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Add a new server
     *
     * @param array{
     *    server_name: string,
     *    ip: string,
     *    pass: string,
     *    sgid: int,
     *    locked?: bool,
     *    internal_ip?: string
     * } $serverData Server creation data
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function create(array $serverData, bool $raw = false): array
    {
        // Validate required fields
        if (! isset($serverData['server_name'])) {
            throw new VirtualizorApiException('Server name is required');
        }
        if (! isset($serverData['ip'])) {
            throw new VirtualizorApiException('Server IP is required');
        }
        if (! isset($serverData['pass'])) {
            throw new VirtualizorApiException('Server API password is required');
        }
        if (! isset($serverData['sgid'])) {
            throw new VirtualizorApiException('Server group ID is required');
        }

        try {
            $response = $this->api->addServer($serverData);

            if ($raw) {
                return $response;
            }

            if (! empty($response['saved'])) {
                return [
                    'success' => true,
                    'id' => (int) $response['saved'],
                    'name' => $serverData['server_name'],
                    'ip' => $serverData['ip'],
                    'server_groups' => $response['servergroups'] ?? [],
                ];
            }

            throw new VirtualizorApiException('Failed to create server: Unknown error');
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to create server: '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Edit a server
     *
     * @param array{
     *    server_name?: string,
     *    ip?: string,
     *    pass?: string,
     *    sgid?: int,
     *    locked?: bool,
     *    internal_ip?: string
     * } $serverData Server update data
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function update(int $serverId, array $serverData, bool $raw = false): array
    {
        // Validate required fields for slave servers
        if (isset($serverData['server_name']) && empty($serverData['server_name'])) {
            throw new VirtualizorApiException('Server name cannot be empty');
        }
        if (isset($serverData['ip']) && empty($serverData['ip'])) {
            throw new VirtualizorApiException('Server IP cannot be empty');
        }

        // Convert locked boolean to integer if provided
        if (isset($serverData['locked'])) {
            $serverData['locked'] = $serverData['locked'] ? 1 : null;
        }

        try {
            $response = $this->api->editServer($serverId, $serverData);

            if ($raw) {
                return $response;
            }

            if (! empty($response['saved'])) {
                return [
                    'success' => true,
                    'id' => $serverId,
                    'server' => $response['serv'] ?? [],
                    'server_groups' => $response['servergroups'] ?? [],
                ];
            }

            throw new VirtualizorApiException('Failed to update server: Unknown error');
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to update server {$serverId}: ".$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Delete a server
     *
     * @param  int  $serverId  Server ID to delete
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function delete(int $serverId, bool $raw = false): array
    {
        try {
            $response = $this->api->deleteServer($serverId);

            if ($raw) {
                return $response;
            }

            return [
                'success' => true,
                'id' => $serverId,
                'servers' => $response['servs'] ?? [],
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to delete server {$serverId}: ".$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Get server loads information
     *
     * @param  bool  $raw  Return raw API response
     *
     * @throws VirtualizorApiException
     */
    public function loads(bool $raw = false): array
    {
        try {
            $response = $this->api->getServerLoads();

            if ($raw) {
                return $response;
            }

            return [
                'loads' => $response['vpsusage'] ?? [],
                'timestamp' => $response['timenow'] ?? null,
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to get server loads: '.$e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Delete a Virtual Private Server
     *
     * @param  int  $vpsId  The ID of the VPS to delete
     * @return array Response from the API
     *
     * @throws VirtualizorException
     */
    public function deleteVps(int $vpsId): array
    {
        $response = $this->client->delete_vs($vpsId);

        if (! isset($response['done']) || $response['done'] !== true) {
            throw new VirtualizorException('Failed to delete VPS');
        }

        return $response;
    }
}
