<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

class FirewallManager
{
    public function __construct(
        private readonly AdminApi $api
    ) {}

    /**
     * Get firewall status
     *
     * @param bool $raw Return raw API response
     * @return array|bool Returns true if running when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function status(bool $raw = false): array|bool
    {
        try {
            $response = $this->api->firewallStatus();

            if ($raw) {
                return $response;
            }

            // Check if output is array or string
            if (isset($response['output']) && is_array($response['output'])) {
                return !str_contains(implode(' ', $response['output']), 'not running');
            }

            // Check firewall_info if output is not available
            return isset($response['firewall_info']) && 
                   isset($response['firewall_info']['firewall_enable']) && 
                   $response['firewall_info']['firewall_enable'] === 1;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to get firewall status: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Start firewall service
     *
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function start(bool $raw = false): array|bool
    {
        try {
            $response = $this->api->firewallStart();

            if ($raw) {
                return $response;
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to start firewall: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Stop firewall service
     *
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function stop(bool $raw = false): array|bool
    {
        try {
            $response = $this->api->firewallStop();

            if ($raw) {
                return $response;
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to stop firewall: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Restart firewall service
     *
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function restart(bool $raw = false): array|bool
    {
        try {
            $response = $this->api->firewallRestart();

            if ($raw) {
                return $response;
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to restart firewall: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Block port(s)
     *
     * @param int|array $ports Single port or array of ports to block
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function blockPort(int|array $ports, bool $raw = false): array|bool
    {
        try {
            $portList = is_array($ports) ? implode(',', $ports) : $ports;
            $response = $this->api->firewallBlockPort($portList);

            if ($raw) {
                return $response;
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to block port(s): ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Allow port(s)
     *
     * @param int|array $ports Single port or array of ports to allow
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function allowPort(int|array $ports, bool $raw = false): array|bool
    {
        try {
            $portList = is_array($ports) ? implode(',', $ports) : $ports;
            $response = $this->api->firewallAllowPort($portList);

            if ($raw) {
                return $response;
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to allow port(s): ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Block IP address
     *
     * @param string $ip IP address to block
     * @param array|null $ports Optional ports to block (null for all ports)
     * @param bool $temporary Make rule temporary
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function blockIp(string $ip, ?array $ports = null, bool $temporary = false, bool $raw = false): array|bool
    {
        try {
            $params = ['ip' => $ip];
            
            if ($ports !== null) {
                $params['ip_port'] = implode(',', $ports);
            }
            
            if ($temporary) {
                $params['ip_temp'] = 1;
            }

            $response = $this->api->firewallBlockIp($params);

            if ($raw) {
                return $response;
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to block IP: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Allow IP address
     *
     * @param string $ip IP address to allow
     * @param array|null $ports Optional ports to allow (null for all ports)
     * @param bool $temporary Make rule temporary
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function allowIp(string $ip, ?array $ports = null, bool $temporary = false, bool $raw = false): array|bool
    {
        try {
            $params = ['ip' => $ip];
            
            if ($ports !== null) {
                $params['ip_port'] = implode(',', $ports);
            }
            
            if ($temporary) {
                $params['ip_temp'] = 1;
            }

            $response = $this->api->firewallAllowIp($params);

            if ($raw) {
                return $response;
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to allow IP: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Search for IP rules
     *
     * @param string $ip IP address to search for
     * @param bool $raw Return raw API response
     * @return array Returns matching rules when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function searchIp(string $ip, bool $raw = false): array
    {
        try {
            $response = $this->api->firewallSearchIp($ip);

            if ($raw) {
                return $response;
            }

            return $response['output'] ?? [];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to search IP rules: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * View current firewall rules
     *
     * @param bool $raw Return raw API response
     * @return array Returns current rules when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function viewRules(bool $raw = false): array
    {
        try {
            $response = $this->api->firewallViewRules();

            if ($raw) {
                return $response;
            }

            return [
                'ports' => [
                    'allowed' => $response['refined_ports']['ACCEPT'] ?? [],
                    'blocked' => $response['refined_ports']['DROP'] ?? []
                ],
                'ips' => [
                    'allowed' => $response['refined_ips']['ACCEPT'] ?? [],
                    'blocked' => $response['refined_ips']['DROP'] ?? []
                ],
                'test_mode' => [
                    'enabled' => (bool) ($response['firewall_info']['test_mode'] ?? false),
                    'duration' => (int) ($response['firewall_info']['mins'] ?? 0)
                ],
                'rules' => $response['view_rule'] ?? [],
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to view firewall rules: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Toggle testing mode
     *
     * @param bool $enable Enable or disable testing mode
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function toggleTestingMode(bool $enable, bool $raw = false): array|bool
    {
        try {
            $response = $this->api->firewallToggleTestingMode($enable);

            if ($raw) {
                return $response;
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to toggle testing mode: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }
} 