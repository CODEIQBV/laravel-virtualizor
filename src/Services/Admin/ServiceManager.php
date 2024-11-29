<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

class ServiceManager
{
    public function __construct(
        private readonly AdminApi $api
    ) {}

    /**
     * List services
     *
     * @param int|null $serverId Server ID to fetch services from (null for master server)
     * @param bool $raw Return raw API response
     * @return array Returns formatted service info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function list(?int $serverId = null, bool $raw = false): array
    {
        try {
            $params = [];
            if ($serverId !== null) {
                $params['changeserid'] = $serverId;
            }

            $response = $this->api->listServices($params);

            if ($raw) {
                return $response;
            }

            return [
                'services' => array_values($response['services'] ?? []),
                'autostart' => $response['autostart'] ?? [],
                'running' => $response['running'] ?? [],
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to list services: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Available service actions
     */
    private const SERVICE_ACTIONS = ['start', 'stop', 'restart'];

    /**
     * Manage services
     *
     * @param array $serviceNames Array of service names to manage
     * @param string $action Action to perform (start, stop, restart)
     * @param int|null $serverId Server ID to manage services on (null for master server)
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function manage(array $serviceNames, string $action, ?int $serverId = null, bool $raw = false): array|bool
    {
        try {
            // Validate service names
            if (empty($serviceNames)) {
                throw new VirtualizorApiException('At least one service name is required');
            }

            // Validate action
            if (!in_array($action, self::SERVICE_ACTIONS)) {
                throw new VirtualizorApiException(
                    'Invalid action. Available actions: ' . implode(', ', self::SERVICE_ACTIONS)
                );
            }

            $params = [
                'sel_serv' => $serviceNames,
                'action' => $action
            ];

            if ($serverId !== null) {
                $params['changeserid'] = $serverId;
            }

            $response = $this->api->manageServices($params);

            if ($raw) {
                return $response;
            }

            return [
                'services' => array_values($response['services'] ?? []),
                'autostart' => $response['autostart'] ?? [],
                'running' => $response['running'] ?? [],
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to manage services: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Available service names
     */
    private const SERVICE_NAMES = [
        'webserver' => 'Web service',
        'network' => 'Network service',
        'sendmail' => 'Mail service',
        'mysqld' => 'MySQL service',
        'iptables' => 'IPTables service'
    ];

    /**
     * Restart a service
     *
     * @param string $serviceName Name of service to restart
     * @param int|null $serverId Server ID to restart service on (null for master server)
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function restart(string $serviceName, ?int $serverId = null, bool $raw = false): array|bool
    {
        try {
            // Validate service name
            if (!array_key_exists($serviceName, self::SERVICE_NAMES)) {
                throw new VirtualizorApiException(
                    'Invalid service name. Available services: ' . implode(', ', array_keys(self::SERVICE_NAMES))
                );
            }

            $params = ['service' => $serviceName];
            if ($serverId !== null) {
                $params['changeserid'] = $serverId;
            }

            $response = $this->api->restartService($params);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to restart service: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to restart service {$serviceName}: " . $e->getMessage(),
                $e->getContext()
            );
        }
    }
} 