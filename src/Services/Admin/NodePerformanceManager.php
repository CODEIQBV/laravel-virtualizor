<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

class NodePerformanceManager
{
    public function __construct(
        private readonly AdminApi $api
    ) {}

    /**
     * Get node performance statistics
     *
     * @param array{
     *    show?: string,
     *    serid?: int
     * } $params Query parameters
     * @param bool $raw Return raw API response
     * @return array Returns formatted stats when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function stats(array $params = [], bool $raw = false): array
    {
        try {
            // Validate month format if provided
            if (isset($params['show']) && !preg_match('/^\d{6}$/', $params['show'])) {
                throw new VirtualizorApiException('show parameter must be in YYYYMM format (e.g. 202401)');
            }

            $response = $this->api->getNodePerformance($params);

            if ($raw) {
                return $response;
            }

            $stats = [];
            foreach ($response['server_stats'] ?? [] as $stat) {
                $stats[] = [
                    'server_id' => (int) $stat[0],
                    'timestamp' => (int) $stat[1],
                    'resources' => [
                        'disk' => (float) $stat[2],
                        'inode' => (int) $stat[3],
                        'ram' => (int) $stat[4],
                        'cpu' => [
                            'usage' => (float) $stat[5],
                            'actual' => (float) $stat[6]
                        ],
                        'network' => [
                            'in' => (int) $stat[7],
                            'out' => (int) $stat[8]
                        ]
                    ]
                ];
            }

            return [
                'stats' => $stats,
                'month' => $response['month'] ?? null,
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to get node performance stats: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }
} 