<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

class ProcessManager
{
    public function __construct(
        private readonly AdminApi $api
    ) {}

    /**
     * List processes
     *
     * @param int|null $serverId Server ID to fetch processes from (null for master server)
     * @param bool $raw Return raw API response
     * @return array Returns formatted process info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function list(?int $serverId = null, bool $raw = false): array
    {
        try {
            $params = [];
            if ($serverId !== null) {
                $params['changeserid'] = $serverId;
            }

            $response = $this->api->listProcesses($params);

            if ($raw) {
                return $response;
            }

            return [
                'processes' => array_map(function($process) {
                    return [
                        'pid' => (int) $process['PID'],
                        'user' => $process['USER'],
                        'cpu' => (float) $process['%CPU'],
                        'memory' => [
                            'percent' => (float) $process['%MEM'],
                            'rss' => (int) $process['RSS']
                        ],
                        'tty' => $process['TT'],
                        'state' => $process['STAT'],
                        'time' => $process['TIME'],
                        'command' => $process['COMMAND']
                    ];
                }, $response['processes'] ?? []),
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to list processes: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Kill process(es)
     *
     * @param array $processIds Array of process IDs to kill
     * @param int|null $serverId Server ID to kill processes on (null for master server)
     * @param bool $raw Return raw API response
     * @return array Returns formatted process info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function kill(array $processIds, ?int $serverId = null, bool $raw = false): array
    {
        try {
            if (empty($processIds)) {
                throw new VirtualizorApiException('At least one process ID is required');
            }

            $params = [];
            if ($serverId !== null) {
                $params['changeserid'] = $serverId;
            }

            $response = $this->api->killProcesses($processIds, $params);

            if ($raw) {
                return $response;
            }

            return [
                'processes' => array_map(function($process) {
                    return [
                        'pid' => (int) $process['PID'],
                        'user' => $process['USER'],
                        'cpu' => (float) $process['%CPU'],
                        'memory' => [
                            'percent' => (float) $process['%MEM'],
                            'rss' => (int) $process['RSS']
                        ],
                        'tty' => $process['TT'],
                        'state' => $process['STAT'],
                        'time' => $process['TIME'],
                        'command' => $process['COMMAND']
                    ];
                }, $response['processes'] ?? []),
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to kill processes: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }
} 