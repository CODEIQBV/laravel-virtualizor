<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

class ServerMonitoringManager
{
    /**
     * Available monitoring modes
     */
    private const MODES = [
        'DEFAULT' => 'default',
        'LIVE' => 'live_stats',
        'NETWORK' => 'network_stats'
    ];

    public function __construct(
        private readonly AdminApi $api
    ) {}

    /**
     * Get server monitoring information
     *
     * @param int $serverId Server ID to monitor
     * @param string|null $mode Monitoring mode (default, live_stats, network_stats)
     * @param bool $raw Return raw API response
     * @return array Returns formatted monitoring info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function get(int $serverId, ?string $mode = null, bool $raw = false): array
    {
        try {
            // Validate mode if provided
            if ($mode !== null && !in_array($mode, self::MODES)) {
                throw new VirtualizorApiException(
                    'Invalid monitoring mode. Available modes: ' . implode(', ', self::MODES)
                );
            }

            $response = $this->api->getServerMonitoring($serverId, $mode);

            if ($raw) {
                return $response;
            }

            $monitoring = [
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];

            // Add default mode data
            if ($mode === null || $mode === self::MODES['DEFAULT']) {
                $monitoring['hardware'] = [
                    'disks' => $response['disks'] ?? [],
                    'ram' => $response['ram_specs'] ?? [],
                    'cpu' => $response['cpu_specs'] ?? [],
                    'partitions' => [
                        'space' => $response['disks_df'] ?? [],
                        'inodes' => $response['disks_inodes'] ?? []
                    ]
                ];
            }

            // Add live stats data
            if ($mode === self::MODES['LIVE']) {
                $monitoring['performance'] = [
                    'cpu' => [
                        'model' => $response['perfomance']['cpu_model'] ?? null,
                        'usage' => (float) ($response['perfomance']['cpu'] ?? 0)
                    ],
                    'ram' => [
                        'usage' => (float) ($response['perfomance']['ram'] ?? 0)
                    ],
                    'disk' => [
                        'health' => $response['disk_health'] ?? [],
                        'io' => $this->formatDiskIO($response['get_disk_io'] ?? [])
                    ],
                    'processes' => [
                        'top_cpu' => $this->formatProcessList($response['filter_ps_cpu'] ?? []),
                        'top_ram' => $this->formatProcessList($response['filter_ps_ram'] ?? [])
                    ],
                    'running_scripts' => $response['crons'] ?? [],
                    'missing_binaries' => $response['binaries_not_found'] ?? []
                ];
            }

            // Add network stats data
            if ($mode === self::MODES['NETWORK']) {
                $monitoring['network'] = [
                    'interfaces' => $response['interface_speed'] ?? []
                ];
            }

            return $monitoring;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to get server monitoring for server {$serverId}: " . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Format disk IO information
     *
     * @param array $diskIO Raw disk IO data
     * @return array Formatted disk IO data
     */
    private function formatDiskIO(array $diskIO): array
    {
        $formatted = [];
        foreach ($diskIO as $disk) {
            if (!empty($disk[0])) {
                $formatted[$disk[0]] = [
                    'utilization' => (float) ($disk[1] ?? 0),
                    'read_speed' => (float) ($disk[2] ?? 0),
                    'write_speed' => (float) ($disk[3] ?? 0),
                    'reads' => (int) ($disk[4] ?? 0),
                    'writes' => (int) ($disk[5] ?? 0)
                ];
            }
        }
        return $formatted;
    }

    /**
     * Format process list
     *
     * @param array $processes Raw process data
     * @return array Formatted process data
     */
    private function formatProcessList(array $processes): array
    {
        $formatted = [];
        foreach ($processes as $process) {
            if (count($process) >= 6) {
                $formatted[] = [
                    'pid' => (int) $process[1],
                    'ppid' => (int) $process[2],
                    'command' => $process[3],
                    'cpu' => (float) $process[4],
                    'ram' => (float) $process[5]
                ];
            }
        }
        return $formatted;
    }
} 