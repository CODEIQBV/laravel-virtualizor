<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

class RamManager
{
    public function __construct(
        private readonly AdminApi $api
    ) {}

    /**
     * Get RAM information
     *
     * @param int|null $serverId Server ID to fetch RAM info from (null for master server)
     * @param bool $raw Return raw API response
     * @return array Returns formatted RAM info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function info(?int $serverId = null, bool $raw = false): array
    {
        try {
            $params = [];
            if ($serverId !== null) {
                $params['changeserid'] = $serverId;
            }

            $response = $this->api->getRamInfo($params);

            if ($raw) {
                return $response;
            }

            // Check if response contains RAM info directly
            if (isset($response['limit'])) {
                return $this->formatRamInfo($response);
            }

            // Check if response contains RAM info in usage array
            if (isset($response['usage']['ram'])) {
                return $this->formatRamInfo($response['usage']['ram']);
            }

            throw new VirtualizorApiException('Invalid response format');
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to get RAM information: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Format RAM information
     *
     * @param array $ramInfo Raw RAM information
     * @return array Formatted RAM information
     */
    private function formatRamInfo(array $ramInfo): array
    {
        return [
            'physical' => [
                'total' => [
                    'mb' => (float) ($ramInfo['limit'] ?? 0)
                ],
                'used' => [
                    'mb' => (float) ($ramInfo['used'] ?? 0)
                ],
                'free' => [
                    'mb' => (float) ($ramInfo['free'] ?? 0)
                ],
                'usage' => [
                    'percent' => (float) ($ramInfo['percent'] ?? 0),
                    'percent_free' => (float) ($ramInfo['percent_free'] ?? 0)
                ]
            ],
            'swap' => [
                'total' => [
                    'mb' => (float) ($ramInfo['swap'] ?? 0)
                ],
                'used' => [
                    'mb' => (float) ($ramInfo['swap_used'] ?? 0)
                ],
                'free' => [
                    'mb' => (float) ($ramInfo['swap_free'] ?? 0)
                ]
            ]
        ];
    }
} 