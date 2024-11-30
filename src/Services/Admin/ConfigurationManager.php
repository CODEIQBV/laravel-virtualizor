<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

class ConfigurationManager
{
    public function __construct(
        private readonly AdminApi $api
    ) {}

    /**
     * Get master settings
     *
     * @param bool $raw Return raw API response
     * @return array Returns formatted settings when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function getMasterSettings(bool $raw = false): array
    {
        try {
            $response = $this->api->getMasterSettings();

            if ($raw) {
                return $response;
            }

            return [
                'globals' => $response['globals'] ?? [],
                'info' => $response['info'] ?? [],
                'languages' => $response['langs'] ?? [],
                'themes' => $response['skins'] ?? [],
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to get master settings: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Get slave settings
     *
     * @param bool $raw Return raw API response
     * @return array Returns formatted settings when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function getSlaveSettings(bool $raw = false): array
    {
        try {
            $response = $this->api->getSlaveSettings();

            if ($raw) {
                return $response;
            }

            return [
                'globals' => $response['globals'] ?? [],
                'info' => $response['info'] ?? [],
                'languages' => $response['langs'] ?? [],
                'themes' => $response['skins'] ?? [],
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to get slave settings: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Available languages
     */
    private const LANGUAGES = [
        'chinese', 'dutch', 'english', 'french', 'german',
        'polish', 'portuguese', 'russian', 'spanish', 'ukraine'
    ];

    /**
     * Available timezones
     */
    private const TIMEZONES = [
        -12 => '(GMT -12:00) Eniwetok, Kwajalein',
        -11 => '(GMT -11:00) Midway Island, Samoa',
        // ... add all timezones
        12 => '(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka'
    ];

    /**
     * Update master settings
     *
     * @param array $settings Settings to update
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function updateMasterSettings(array $settings, bool $raw = false): array|bool
    {
        try {
            // Validate required fields
            $required = ['sn', 'sess_len', 'soft_email', 'language', 'timezone'];
            foreach ($required as $field) {
                if (!isset($settings[$field])) {
                    throw new VirtualizorApiException("{$field} is required");
                }
            }

            // Validate language
            if (!in_array($settings['language'], self::LANGUAGES)) {
                throw new VirtualizorApiException(
                    'Invalid language. Available languages: ' . implode(', ', self::LANGUAGES)
                );
            }

            // Validate timezone
            if (!array_key_exists($settings['timezone'], self::TIMEZONES)) {
                throw new VirtualizorApiException(
                    'Invalid timezone. Must be between -12 and 12'
                );
            }

            // Add required parameter
            $settings['editsettings'] = 1;

            $response = $this->api->updateMasterSettings($settings);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to update master settings: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to update master settings: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Update slave settings
     *
     * @param array{
     *    serid: int,
     *    cookie_name: string,
     *    soft_email: string,
     *    timezone: int,
     *    cron_time: string,
     *    emps_cron_time: string,
     *    cpu_nm?: bool,
     *    overcommit?: int,
     *    vnc_ip?: string,
     *    node_bandwidth?: int,
     *    vps_bandwidth_threshold?: int,
     *    vpslimit?: int,
     *    vcores?: int,
     *    server_latitude?: int,
     *    server_longitude?: int,
     *    haproxy_enable?: bool,
     *    haproxy_src_ips?: string,
     *    update?: int
     * } $settings Settings to update
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function updateSlaveSettings(array $settings, bool $raw = false): array|bool
    {
        try {
            // Validate required fields
            $required = ['serid', 'cookie_name', 'soft_email', 'timezone', 'cron_time', 'emps_cron_time'];
            foreach ($required as $field) {
                if (!isset($settings[$field])) {
                    throw new VirtualizorApiException("{$field} is required");
                }
            }

            // Validate timezone
            if (!array_key_exists($settings['timezone'], self::TIMEZONES)) {
                throw new VirtualizorApiException(
                    'Invalid timezone. Must be between -12 and 12'
                );
            }

            // Validate cron expressions
            if (!$this->isValidCronExpression($settings['cron_time'])) {
                throw new VirtualizorApiException('Invalid cron_time format');
            }
            if (!$this->isValidCronExpression($settings['emps_cron_time'])) {
                throw new VirtualizorApiException('Invalid emps_cron_time format');
            }

            // Convert boolean values to integers
            foreach (['cpu_nm', 'haproxy_enable'] as $field) {
                if (isset($settings[$field])) {
                    $settings[$field] = $settings[$field] ? 1 : 0;
                }
            }

            // Add required parameter
            $settings['editsettings'] = 1;

            $response = $this->api->updateSlaveSettings($settings);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to update slave settings: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to update slave settings: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Validate cron expression
     *
     * @param string $expression Cron expression to validate
     * @return bool Whether expression is valid
     */
    private function isValidCronExpression(string $expression): bool
    {
        $parts = explode(' ', $expression);
        return count($parts) === 5;
    }

    /**
     * Update Webuzo settings
     *
     * @param array{
     *    noc_apikey?: string,
     *    noc_apipass?: string,
     *    disable_webuzo?: bool,
     *    select_all?: bool,
     *    sel_scripts?: array<int>
     * } $settings Webuzo settings
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function updateWebuzoSettings(array $settings, bool $raw = false): array|bool
    {
        try {
            // Convert boolean values to integers
            foreach (['disable_webuzo', 'select_all'] as $field) {
                if (isset($settings[$field])) {
                    $settings[$field] = $settings[$field] ? 1 : 0;
                }
            }

            $response = $this->api->updateWebuzoSettings($settings);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to update Webuzo settings: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to update Webuzo settings: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Get Webuzo settings
     *
     * @param bool $raw Return raw API response
     * @return array Returns formatted settings when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function getWebuzoSettings(bool $raw = false): array
    {
        try {
            $response = $this->api->getWebuzoSettings();

            if ($raw) {
                return $response;
            }

            return [
                'scripts' => array_map(function($script) {
                    return [
                        'id' => (int) $script['sid'],
                        'parent_id' => (int) $script['parent'],
                        'name' => $script['name'],
                        'soft_name' => $script['softname'],
                        'type' => $script['type'],
                        'category' => $script['category']
                    ];
                }, $response['scripts'] ?? []),
                'enabled_scripts' => array_map('intval', $response['enabled_scripts'] ?? []),
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to get Webuzo settings: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Get Webuzo scripts
     *
     * @param bool $raw Return raw API response
     * @return array Returns formatted script info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function getWebuzoScripts(bool $raw = false): array
    {
        try {
            $response = $this->api->getWebuzoScripts();

            if ($raw) {
                return $response;
            }

            $scripts = [];
            foreach ($response['scripts'] ?? [] as $script) {
                $scripts[] = [
                    'id' => (int) $script['sid'],
                    'parent_id' => (int) $script['parent'],
                    'name' => $script['name'],
                    'soft_name' => $script['softname'],
                    'type' => $script['type'],
                    'category' => $script['category']
                ];
            }

            // Group scripts by category
            $categorized = [];
            foreach ($scripts as $script) {
                $category = $script['category'];
                if (!isset($categorized[$category])) {
                    $categorized[$category] = [];
                }
                $categorized[$category][] = $script;
            }

            return [
                'scripts' => $scripts,
                'by_category' => $categorized,
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to get Webuzo scripts: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Available SMTP security modes
     */
    private const SMTP_SECURITY = [
        'NONE' => 0,
        'SSL' => 1,
        'STARTTLS' => 2
    ];

    /**
     * Update email settings
     *
     * @param array{
     *    use_php_mail: bool,
     *    server?: string,
     *    port?: int,
     *    username?: string,
     *    password?: string,
     *    smtp_security?: int,
     *    connect_timeout?: int,
     *    debug?: bool,
     *    disable_emails?: bool
     * } $settings Email settings
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function updateEmailSettings(array $settings, bool $raw = false): array|bool
    {
        try {
            // Validate required fields
            if (!isset($settings['use_php_mail'])) {
                throw new VirtualizorApiException('use_php_mail setting is required');
            }

            // If using SMTP, validate required SMTP settings
            if (!$settings['use_php_mail']) {
                $required = ['server', 'port', 'username', 'password'];
                foreach ($required as $field) {
                    if (!isset($settings[$field])) {
                        throw new VirtualizorApiException("{$field} is required for SMTP configuration");
                    }
                }

                // Validate port range
                if ($settings['port'] < 1 || $settings['port'] > 65535) {
                    throw new VirtualizorApiException('Port must be between 1 and 65535');
                }

                // Validate SMTP security mode
                if (isset($settings['smtp_security']) && 
                    !in_array($settings['smtp_security'], self::SMTP_SECURITY)) {
                    throw new VirtualizorApiException(
                        'Invalid SMTP security mode. Available modes: NONE, SSL, STARTTLS'
                    );
                }

                // Validate connect timeout
                if (isset($settings['connect_timeout']) && $settings['connect_timeout'] < 5) {
                    throw new VirtualizorApiException('Connect timeout must be at least 5 seconds');
                }
            }

            $params = [
                'editemailconfigsettings' => 1,
                'mail' => $settings['use_php_mail'] ? 1 : 0,
                'mail_server' => $settings['server'] ?? '',
                'mail_port' => $settings['port'] ?? 0,
                'mail_user' => $settings['username'] ?? '',
                'mail_pass' => $settings['password'] ?? '',
                'mail_smtp_secure' => $settings['smtp_security'] ?? 0,
                'mail_connect_timeout' => $settings['connect_timeout'] ?? 0,
                'mail_debug' => isset($settings['debug']) && $settings['debug'] ? 1 : 0,
                'disable_email' => isset($settings['disable_emails']) && $settings['disable_emails'] ? 1 : 0
            ];

            $response = $this->api->updateEmailSettings($params);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to update email settings: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to update email settings: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Get email settings
     *
     * @param bool $raw Return raw API response
     * @return array Returns formatted settings when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function getEmailSettings(bool $raw = false): array
    {
        try {
            $response = $this->api->getEmailSettings();

            if ($raw) {
                return $response;
            }

            return [
                'use_php_mail' => (bool) ($response['info']['mail'] ?? false),
                'server' => $response['info']['mail_server'] ?? null,
                'port' => (int) ($response['info']['mail_port'] ?? 0),
                'username' => $response['info']['mail_user'] ?? null,
                'password' => $response['info']['mail_pass'] ?? null,
                'smtp_security' => (int) ($response['info']['mail_smtp_secure'] ?? 0),
                'connect_timeout' => (int) ($response['info']['mail_connect_timeout'] ?? 0),
                'debug' => (bool) ($response['info']['mail_debug'] ?? false),
                'emails_disabled' => (bool) ($response['info']['disable_email'] ?? false),
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to get email settings: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Get server information
     *
     * @param bool $raw Return raw API response
     * @return array Returns formatted server info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function getServerInfo(bool $raw = false): array
    {
        try {
            $response = $this->api->getServerInfo();

            if ($raw) {
                return $response;
            }

            return [
                'path' => $response['info']['path'] ?? null,
                'api' => [
                    'key' => $response['info']['key'] ?? null,
                    'pass' => $response['info']['pass'] ?? null
                ],
                'kernel' => $response['info']['kernel'] ?? null,
                'vps_count' => (int) ($response['info']['num_vs'] ?? 0),
                'version' => [
                    'number' => $response['info']['version'] ?? null,
                    'patch' => (int) ($response['info']['patch'] ?? 0)
                ],
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to get server information: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Get license information
     *
     * @param bool $raw Return raw API response
     * @return array Returns formatted license info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function getLicenseInfo(bool $raw = false): array
    {
        try {
            $response = $this->api->getLicenseInfo();

            if ($raw) {
                return $response;
            }

            return [
                'license' => [
                    'key' => $response['info']['license'] ?? null,
                    'type' => [
                        'id' => (int) ($response['info']['lictype'] ?? 0),
                        'name' => $response['info']['lictype_txt'] ?? null
                    ],
                    'status' => [
                        'active' => (bool) ($response['info']['active'] ?? false),
                        'text' => $response['info']['active_txt'] ?? null
                    ],
                    'vps_limit' => (int) ($response['info']['licnumvs'] ?? 0),
                    'expiry' => [
                        'date' => $response['info']['licexpires'] ?? null,
                        'text' => $response['info']['licexpires_txt'] ?? null
                    ]
                ],
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to get license information: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Update license key
     *
     * @param string $licenseKey New license key
     * @param bool $refresh Whether to refresh the license after update
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function updateLicense(string $licenseKey, bool $refresh = false, bool $raw = false): array|bool
    {
        try {
            // Validate license key format
            if (!preg_match('/^VIRT[DE]-\d{5}-\d{5}-\d{5}-\d{5}$/', $licenseKey)) {
                throw new VirtualizorApiException(
                    'Invalid license key format. Must be VIRTD-XXXXX-XXXXX-XXXXX-XXXXX or VIRTE-XXXXX-XXXXX-XXXXX-XXXXX'
                );
            }

            $response = $this->api->updateLicense($licenseKey, $refresh);

            if ($raw) {
                return $response;
            }

            if (empty($response['done']) || $response['done'] !== 'Updated License Key') {
                throw new VirtualizorApiException(
                    'Failed to update license: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to update license: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Change server hostname
     *
     * @param string $hostname New hostname
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function changeHostname(string $hostname, bool $raw = false): array|bool
    {
        try {
            // Validate hostname format
            if (!filter_var($hostname, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                throw new VirtualizorApiException(
                    'Invalid hostname format. Must be a valid domain name (e.g., server.example.com)'
                );
            }

            $response = $this->api->changeHostname($hostname);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to change hostname: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to change hostname: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Enable maintenance mode
     *
     * @param array{
     *    subject?: string,
     *    message?: string
     * } $options Maintenance mode options
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function enableMaintenanceMode(array $options = [], bool $raw = false): array|bool
    {
        try {
            $params = [
                'off' => 1,
                'maintenance' => 1,
                'off_subject' => $options['subject'] ?? 'System Maintenance',
                'off_message' => $options['message'] ?? 'The system is currently undergoing maintenance.'
            ];

            $response = $this->api->setMaintenanceMode($params);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to enable maintenance mode: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to enable maintenance mode: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Disable maintenance mode
     *
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function disableMaintenanceMode(bool $raw = false): array|bool
    {
        try {
            $response = $this->api->setMaintenanceMode([
                'off' => 0,
                'maintenance' => 0
            ]);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to disable maintenance mode: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to disable maintenance mode: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Get kernel configuration
     *
     * @param bool $raw Return raw API response
     * @return array Returns formatted kernel config when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function getKernelConfig(bool $raw = false): array
    {
        try {
            $response = $this->api->getKernelConfig();

            if ($raw) {
                return $response;
            }

            // Parse kernel configuration into structured array
            $config = [];
            $currentSection = 'global';
            
            $lines = explode("\n", $response['kernelconf'] ?? '');
            foreach ($lines as $line) {
                $line = trim($line);
                
                // Skip empty lines and comments
                if (empty($line) || str_starts_with($line, '#')) {
                    continue;
                }

                // Check for section comments
                if (str_starts_with($line, '##')) {
                    $section = trim(str_replace('##', '', $line));
                    $currentSection = strtolower(str_replace(' ', '_', $section));
                    continue;
                }

                // Parse key-value pairs
                if (str_contains($line, '=')) {
                    [$key, $value] = explode('=', $line, 2);
                    $config[$currentSection][trim($key)] = trim($value, '"');
                }
            }

            return [
                'config' => $config,
                'raw_config' => $response['kernelconf'] ?? null,
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to get kernel configuration: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Get default VPS configuration
     *
     * @param bool $raw Return raw API response
     * @return array Returns formatted config when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function getDefaultVpsConfig(bool $raw = false): array
    {
        try {
            $response = $this->api->getDefaultVpsConfig();

            if ($raw) {
                return $response;
            }

            // Parse configuration into structured array
            $config = [];
            $currentSection = 'general';
            
            $lines = explode("\n", $response['defaultvsconf'] ?? '');
            foreach ($lines as $line) {
                $line = trim($line);
                
                // Skip empty lines and comments
                if (empty($line) || str_starts_with($line, '#')) {
                    continue;
                }

                // Parse key-value pairs
                if (str_contains($line, '=')) {
                    [$key, $value] = explode('=', $line, 2);
                    $config[trim($key)] = trim($value, '"');
                }
            }

            return [
                'config' => $config,
                'raw_config' => $response['defaultvsconf'] ?? null,
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to get default VPS configuration: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Update default VPS configuration
     *
     * @param string $config New configuration
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function updateDefaultVpsConfig(string $config, bool $raw = false): array|bool
    {
        try {
            $response = $this->api->updateDefaultVpsConfig($config);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to update default VPS configuration: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to update default VPS configuration: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Check for and apply updates
     *
     * @param bool $raw Return raw API response
     * @return array Returns formatted update info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function checkUpdates(bool $raw = false): array
    {
        try {
            $response = $this->api->checkUpdates();

            if ($raw) {
                return $response;
            }

            return [
                'updated' => (bool) ($response['updated'] ?? false),
                'version' => [
                    'number' => $response['info']['version'] ?? null,
                    'patch' => (int) ($response['info']['patch'] ?? 0)
                ],
                'update' => [
                    'message' => $response['info']['message'] ?? null,
                    'mode' => (int) ($response['info']['mode'] ?? 0),
                    'link' => $response['info']['link'] ?? null,
                    'redirect' => $response['info']['redirect'] ?? null
                ],
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to check updates: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Apply available updates
     *
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function applyUpdates(bool $raw = false): array|bool
    {
        try {
            $response = $this->api->applyUpdates();

            if ($raw) {
                return $response;
            }

            if (empty($response['updated'])) {
                throw new VirtualizorApiException(
                    'Failed to apply updates: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to apply updates: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Get email templates
     *
     * @param bool $raw Return raw API response
     * @return array Returns formatted templates when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function getEmailTemplates(bool $raw = false): array
    {
        try {
            $response = $this->api->getEmailTemplates();

            if ($raw) {
                return $response;
            }

            $templates = [];
            foreach ($response['emailtemps'] ?? [] as $key => $template) {
                // Group templates by category
                $category = $this->getTemplateCategory($key);
                
                if (!isset($templates[$category])) {
                    $templates[$category] = [];
                }

                $templates[$category][$key] = [
                    'name' => $key,
                    'subject' => $template['title'] ?? null,
                    'body' => $template['body'] ?? null
                ];
            }

            return [
                'templates' => $templates,
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to get email templates: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Get template category based on template name
     *
     * @param string $templateName Template name
     * @return string Category name
     */
    private function getTemplateCategory(string $templateName): string
    {
        if (str_contains($templateName, 'admin_')) {
            return 'admin';
        }
        if (str_contains($templateName, 'billing_')) {
            return 'billing';
        }
        if (str_contains($templateName, 'suspend_') || str_contains($templateName, 'unsuspend_')) {
            return 'suspension';
        }
        if (str_contains($templateName, 'webuzo')) {
            return 'webuzo';
        }
        return 'general';
    }

    /**
     * Update email template
     *
     * @param string $templateName Template name to update
     * @param string $subject New email subject
     * @param string $content New email content
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function updateEmailTemplate(
        string $templateName, 
        string $subject, 
        string $content, 
        bool $raw = false
    ): array|bool {
        try {
            // Get available templates to validate template name
            $templates = $this->getEmailTemplates();
            $found = false;

            foreach ($templates['templates'] as $category) {
                if (isset($category[$templateName])) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                throw new VirtualizorApiException(
                    "Invalid template name: {$templateName}. Use getEmailTemplates() to get available templates."
                );
            }

            // Validate subject and content are not empty
            if (empty($subject)) {
                throw new VirtualizorApiException('Email subject cannot be empty');
            }
            if (empty($content)) {
                throw new VirtualizorApiException('Email content cannot be empty');
            }

            $response = $this->api->updateEmailTemplate($templateName, $subject, $content);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to update email template: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to update email template: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Reset email template to default
     *
     * @param string $templateName Template name to reset
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function resetEmailTemplate(string $templateName, bool $raw = false): array|bool
    {
        try {
            // Get available templates to validate template name
            $templates = $this->getEmailTemplates();
            $found = false;

            foreach ($templates['templates'] as $category) {
                if (isset($category[$templateName])) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                throw new VirtualizorApiException(
                    "Invalid template name: {$templateName}. Use getEmailTemplates() to get available templates."
                );
            }

            $response = $this->api->resetEmailTemplate($templateName);

            if ($raw) {
                return $response;
            }

            // The API doesn't return a specific success indicator for reset
            // We'll consider it successful if we get a response with timestamp
            if (empty($response['timenow'])) {
                throw new VirtualizorApiException(
                    'Failed to reset email template: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to reset email template: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }
} 