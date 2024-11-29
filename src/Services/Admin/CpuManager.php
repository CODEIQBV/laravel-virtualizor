<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

class CpuManager
{
    public function __construct(
        private readonly AdminApi $api
    ) {}

    /**
     * Get CPU information
     *
     * @param int|null $serverId Server ID to fetch CPU info from (null for master server)
     * @param bool $raw Return raw API response
     * @return array Returns formatted CPU info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function info(?int $serverId = null, bool $raw = false): array
    {
        try {
            $params = [];
            if ($serverId !== null) {
                $params['changeserid'] = $serverId;
            }

            $response = $this->api->getCpuInfo($params);

            if ($raw) {
                return $response;
            }

            // Check if response contains CPU info directly
            if (isset($response['manu'])) {
                return $this->formatCpuInfo($response);
            }

            // Check if response contains CPU info in usage array
            if (isset($response['usage']['cpu'])) {
                return $this->formatCpuInfo($response['usage']['cpu']);
            }

            throw new VirtualizorApiException('Invalid response format');
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to get CPU information: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Format CPU information
     *
     * @param array $cpuInfo Raw CPU information
     * @return array Formatted CPU information
     */
    private function formatCpuInfo(array $cpuInfo): array
    {
        return [
            'manufacturer' => $cpuInfo['manu'] ?? 'unknown',
            'model' => trim($cpuInfo['cpumodel'] ?? ''),
            'specs' => [
                'limit' => [
                    'mhz' => (float) ($cpuInfo['limit'] ?? 0)
                ],
                'used' => [
                    'mhz' => (float) ($cpuInfo['used'] ?? 0)
                ],
                'free' => [
                    'mhz' => (float) ($cpuInfo['free'] ?? 0)
                ]
            ],
            'usage' => [
                'percent' => (float) ($cpuInfo['percent'] ?? 0),
                'percent_free' => (float) ($cpuInfo['percent_free'] ?? 0)
            ]
        ];
    }
} 