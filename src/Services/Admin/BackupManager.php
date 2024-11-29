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
}
