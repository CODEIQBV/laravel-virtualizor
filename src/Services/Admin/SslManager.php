<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

class SslManager
{
    public function __construct(
        private readonly AdminApi $api
    ) {}

    /**
     * Get SSL files configuration
     *
     * @param bool $raw Return raw API response
     * @return array Returns formatted SSL info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function files(bool $raw = false): array
    {
        try {
            $response = $this->api->getSslFiles();

            if ($raw) {
                return $response;
            }

            return [
                'key' => $response['keyconf'] ?? null,
                'certificate' => $response['crtconf'] ?? null,
                'csr' => $response['csrconf'] ?? null,
                'bundle' => $response['cert_bundle'] ?? null,
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to get SSL files: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Available key sizes
     */
    private const KEY_SIZES = [2048, 3072, 4096, 8192, 'ec-256', 'ec-384'];

    /**
     * Available SSL types
     */
    private const SSL_TYPES = ['zerossl', 'letsencrypt'];

    /**
     * Create SSL certificate
     *
     * @param array{
     *    country: string,
     *    state: string,
     *    locality: string,
     *    organisation: string,
     *    comname: string,
     *    email: string,
     *    keysize: int,
     *    orgunit?: string
     * } $params SSL parameters
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function create(array $params, bool $raw = false): array|bool
    {
        try {
            // Validate required fields
            $required = ['country', 'state', 'locality', 'organisation', 'comname', 'email', 'keysize'];
            foreach ($required as $field) {
                if (!isset($params[$field])) {
                    throw new VirtualizorApiException("{$field} is required");
                }
            }

            // Validate country code
            if (!preg_match('/^[A-Z]{2}$/', $params['country'])) {
                throw new VirtualizorApiException('country must be a 2-letter country code (e.g. US, GB)');
            }

            // Validate email
            if (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
                throw new VirtualizorApiException('Invalid email address');
            }

            // Validate key size
            if (!in_array($params['keysize'], self::KEY_SIZES)) {
                throw new VirtualizorApiException(
                    'Invalid key size. Available sizes: ' . implode(', ', self::KEY_SIZES)
                );
            }

            $response = $this->api->createSsl($params);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to create SSL certificate: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to create SSL certificate: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Install Let's Encrypt certificate
     *
     * @param array{
     *    primary_domain: string,
     *    contact_email: string,
     *    key_size: int|string,
     *    renew_days: int,
     *    staging?: bool,
     *    enable_force?: bool,
     *    ssl_type?: string
     * } $params Certificate parameters
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function installLetsEncrypt(array $params, bool $raw = false): array|bool
    {
        try {
            // Validate required fields
            $required = ['primary_domain', 'contact_email', 'key_size', 'renew_days'];
            foreach ($required as $field) {
                if (!isset($params[$field])) {
                    throw new VirtualizorApiException("{$field} is required");
                }
            }

            // Validate email
            if (!filter_var($params['contact_email'], FILTER_VALIDATE_EMAIL)) {
                throw new VirtualizorApiException('Invalid email address');
            }

            // Validate key size
            if (!in_array($params['key_size'], self::KEY_SIZES)) {
                throw new VirtualizorApiException(
                    'Invalid key size. Available sizes: ' . implode(', ', self::KEY_SIZES)
                );
            }

            // Validate renew days
            if ($params['renew_days'] < 1 || $params['renew_days'] > 89) {
                throw new VirtualizorApiException('renew_days must be between 1 and 89');
            }

            // Validate SSL type if provided
            if (isset($params['ssl_type']) && !in_array($params['ssl_type'], self::SSL_TYPES)) {
                throw new VirtualizorApiException(
                    'Invalid SSL type. Available types: ' . implode(', ', self::SSL_TYPES)
                );
            }

            // Convert boolean values to integers
            if (isset($params['staging'])) {
                $params['staging'] = $params['staging'] ? 1 : 0;
            }

            if (isset($params['enable_force'])) {
                $params['enable_force'] = $params['enable_force'] ? 1 : 0;
            }

            $response = $this->api->installLetsEncrypt($params);

            if ($raw) {
                return $response;
            }

            if (empty($response['done']) || $response['done'] !== 'Configuration saved successfully') {
                throw new VirtualizorApiException(
                    'Failed to install Let\'s Encrypt certificate: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to install Let\'s Encrypt certificate: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Renew Let's Encrypt certificate
     *
     * @param bool $raw Return raw API response
     * @return array|int Returns task ID when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function renewLetsEncrypt(bool $raw = false): array|int
    {
        try {
            $response = $this->api->renewLetsEncrypt();

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to renew Let\'s Encrypt certificate: Operation unsuccessful'
                );
            }

            return (int) $response['done'];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to renew Let\'s Encrypt certificate: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Show Let's Encrypt logs
     *
     * @param bool $raw Return raw API response
     * @return array Returns formatted log info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function showLetsEncryptLogs(bool $raw = false): array
    {
        try {
            $response = $this->api->showLetsEncryptLogs();

            if ($raw) {
                return $response;
            }

            return [
                'config' => [
                    'domain' => $response['crt_config_options']['crt_save_cfg_frm']['inputs']['primary_domain']['value'] ?? null,
                    'email' => $response['crt_config_options']['crt_save_cfg_frm']['inputs']['contact_email']['value'] ?? null,
                    'key_size' => (int) ($response['crt_config_options']['crt_save_cfg_frm']['inputs']['key_size']['value'] ?? 0),
                    'renew_days' => (int) ($response['crt_config_options']['crt_save_cfg_frm']['inputs']['renew_days']['value'] ?? 0),
                    'staging' => (bool) ($response['crt_config_options']['crt_save_cfg_frm']['inputs']['staging']['value'] ?? false),
                    'force_enabled' => (bool) ($response['crt_config_options']['crt_save_cfg_frm']['inputs']['enable_force']['value'] ?? false)
                ],
                'certificate' => [
                    'domain' => $response['crt_details']['crt_domain'] ?? null,
                    'san' => $response['crt_details']['crt_san'] ?? null,
                    'issuer' => $response['crt_details']['crt_issuer'] ?? null,
                    'serial' => $response['crt_details']['crt_serialno'] ?? null,
                    'validity' => [
                        'from' => $response['crt_details']['crt_valid_from_time'] ?? null,
                        'to' => $response['crt_details']['crt_valid_to_time'] ?? null,
                        'next_renewal' => $response['crt_details']['next_renew'] ?? null
                    ],
                    'is_installed' => str_contains($response['crt_details']['crt_installed'] ?? '', 'Yes')
                ],
                'logs' => $response['logs'] ?? null,
                'task_id' => $response['actid'] ?? null,
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to show Let\'s Encrypt logs: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }
} 