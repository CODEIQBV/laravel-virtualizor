<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

class SshKeyManager
{
    public function __construct(
        private readonly AdminApi $api
    ) {}

    /**
     * List SSH keys for a user
     *
     * @param int $userId User ID to list SSH keys for
     * @param bool $raw Return raw API response
     * @return array Returns formatted SSH key info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function list(int $userId, bool $raw = false): array
    {
        try {
            $response = $this->api->listSshKeys($userId);

            if ($raw) {
                return $response;
            }

            $keys = [];
            foreach ($response['ssh_keys'] ?? [] as $id => $key) {
                $keys[] = [
                    'id' => (int) $key['keyid'],
                    'name' => $key['name'],
                    'value' => $key['value']
                ];
            }

            return [
                'keys' => $keys,
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to list SSH keys for user {$userId}: " . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Add SSH keys to a VPS
     *
     * @param int $vpsId VPS ID to add SSH keys to
     * @param array<int> $sshKeyIds Array of SSH key IDs to add
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function addToVps(int $vpsId, array $sshKeyIds, bool $raw = false): array|bool
    {
        try {
            if (empty($sshKeyIds)) {
                throw new VirtualizorApiException('At least one SSH key ID is required');
            }

            $response = $this->api->addSshKeys($vpsId, $sshKeyIds);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to add SSH keys: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to add SSH keys to VPS {$vpsId}: " . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Generate new SSH key pair
     *
     * @param bool $raw Return raw API response
     * @return array Returns formatted key info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function generate(bool $raw = false): array
    {
        try {
            $response = $this->api->generateSshKeys();

            if ($raw) {
                return $response;
            }

            return [
                'keys' => [
                    'public' => $response['public_key'],
                    'private' => $response['private_key']
                ],
                'path' => $response['path']
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to generate SSH keys: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }
} 