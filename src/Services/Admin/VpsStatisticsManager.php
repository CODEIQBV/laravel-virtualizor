<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

class VpsStatisticsManager
{
    public function __construct(
        private readonly AdminApi $api
    ) {}

    /**
     * Get VPS statistics
     *
     * @param array{
     *    vpsid?: int,
     *    serid?: int,
     *    svs?: int,
     *    show?: string
     * } $params Query parameters
     * @param bool $raw Return raw API response
     * @return array Returns formatted statistics when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function get(array $params = [], bool $raw = false): array
    {
        try {
            // Validate month format if provided
            if (isset($params['show']) && !preg_match('/^\d{6}$/', $params['show'])) {
                throw new VirtualizorApiException('show parameter must be in YYYYMM format (e.g. 202401)');
            }

            $response = $this->api->getVpsStats($params);

            if ($raw) {
                return $response;
            }

            return [
                'server' => [
                    'bandwidth' => $response['pie_data']['server_bandwidth'] ?? null,
                    'cpu' => $response['pie_data']['server_cpu'] ?? null,
                    'ram' => $response['pie_data']['server_ram'] ?? null
                ],
                'vps_stats' => $this->formatVpsStats($response['vps_stats'] ?? null),
                'vps_data' => $response['vps_data'] ?? null,
                'month' => $response['month'] ?? null,
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to get VPS statistics: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Format VPS statistics data
     *
     * @param array|null $stats Raw statistics data
     * @return array|null Formatted statistics data
     */
    private function formatVpsStats(?array $stats): ?array
    {
        if (!$stats) {
            return null;
        }

        return array_map(function($stat) {
            return [
                'vps_id' => (int) $stat[0],
                'timestamp' => (int) $stat[1],
                'status' => (bool) $stat[2],
                'disk' => (float) $stat[3],
                'inode' => (int) $stat[4],
                'ram' => (int) $stat[5],
                'cpu' => [
                    'usage' => (float) $stat[6],
                    'actual' => (float) $stat[7]
                ],
                'network' => [
                    'in' => (int) $stat[8],
                    'out' => (int) $stat[9]
                ],
                'io' => [
                    'read' => (int) $stat[10],
                    'write' => (int) $stat[11]
                ]
            ];
        }, $stats);
    }
} 