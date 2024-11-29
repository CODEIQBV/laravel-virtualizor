<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

class DiskManager
{
    public function __construct(
        private readonly AdminApi $api
    ) {}

    /**
     * Get disk information
     *
     * @param int|null $serverId Server ID to fetch disk info from (null for master server)
     * @param bool $raw Return raw API response
     * @return array Returns formatted disk info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function info(?int $serverId = null, bool $raw = false): array
    {
        try {
            $params = [];
            if ($serverId !== null) {
                $params['changeserid'] = $serverId;
            }

            $response = $this->api->getDiskInfo($params);

            if ($raw) {
                return $response;
            }

            // Check if response contains disk info directly
            if (isset($response['/'])) {
                return $this->formatDiskInfo($response);
            }

            // Check if response contains disk info in usage array
            if (isset($response['usage']['disk'])) {
                return $this->formatDiskInfo($response['usage']['disk']);
            }

            throw new VirtualizorApiException('Invalid response format');
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to get disk information: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Format disk information
     *
     * @param array $diskInfo Raw disk information
     * @return array Formatted disk information
     */
    private function formatDiskInfo(array $diskInfo): array
    {
        $formatted = [];
        foreach ($diskInfo as $mount => $disk) {
            if (is_array($disk)) {
                $formatted[$mount] = [
                    'size' => [
                        'total' => [
                            'mb' => (float) ($disk['limit'] ?? 0),
                            'gb' => (float) ($disk['limit_gb'] ?? 0)
                        ],
                        'used' => [
                            'mb' => (float) ($disk['used'] ?? 0),
                            'gb' => (float) ($disk['used_gb'] ?? 0),
                            'actual_gb' => (float) ($disk['actual_gb'] ?? 0)
                        ],
                        'available' => [
                            'mb' => (float) ($disk['available'] ?? 0),
                            'gb' => (float) ($disk['free_gb'] ?? 0)
                        ],
                        'free' => [
                            'mb' => (float) ($disk['free'] ?? 0)
                        ]
                    ],
                    'usage' => [
                        'percent' => (float) ($disk['percent'] ?? 0),
                        'percent_free' => (float) ($disk['percent_free'] ?? 0)
                    ]
                ];
            }
        }
        return $formatted;
    }
} 