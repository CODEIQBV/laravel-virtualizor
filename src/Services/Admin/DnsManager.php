<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

class DnsManager
{
    public function __construct(
        private readonly AdminApi $api
    ) {}

    /**
     * List DNS plans
     *
     * @param array{
     *    planname?: string
     * } $filters Search filters
     * @param int $page Page number
     * @param int $perPage Records per page
     * @param bool $raw Return raw API response
     * @return array Returns formatted DNS plan info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function list(array $filters = [], int $page = 1, int $perPage = 50, bool $raw = false): array
    {
        try {
            $response = $this->api->listDnsPlans($filters, $page, $perPage);

            if ($raw) {
                return $response;
            }

            $plans = [];
            foreach ($response['dnsplans'] ?? [] as $id => $plan) {
                $plans[] = [
                    'id' => (int) $plan['dnsplid'],
                    'pdns_id' => (int) $plan['pdnsid'],
                    'name' => $plan['plan_name'],
                    'limits' => [
                        'max_domains' => (int) $plan['max_domains'],
                        'max_domain_records' => (int) $plan['max_domain_records'],
                        'default_ttl' => (int) $plan['def_ttl']
                    ],
                    'server_name' => $plan['dns_server_name']
                ];
            }

            return [
                'plans' => $plans,
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to list DNS plans: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Create a new DNS plan
     *
     * @param array{
     *    plan_name: string,
     *    dnsserverid: int,
     *    maxdomains: int,
     *    maxdomainsrec: int,
     *    ttl: int
     * } $params DNS plan parameters
     * @param bool $raw Return raw API response
     * @return array|int Returns plan ID when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function create(array $params, bool $raw = false): array|int
    {
        try {
            // Validate required fields
            $required = ['plan_name', 'dnsserverid', 'maxdomains', 'maxdomainsrec', 'ttl'];
            foreach ($required as $field) {
                if (!isset($params[$field])) {
                    throw new VirtualizorApiException("{$field} is required");
                }
            }

            // Validate numeric fields
            foreach (['maxdomains', 'maxdomainsrec', 'ttl'] as $field) {
                if (!is_int($params[$field]) || $params[$field] <= 0) {
                    throw new VirtualizorApiException("{$field} must be a positive integer");
                }
            }

            $response = $this->api->addDnsPlan($params);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to create DNS plan: Operation unsuccessful'
                );
            }

            return (int) $response['done'];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to create DNS plan: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Edit a DNS plan
     *
     * @param int $planId DNS plan ID to edit
     * @param array{
     *    plan_name: string,
     *    dnsserverid: int,
     *    maxdomains: int,
     *    maxdomainsrec: int,
     *    ttl: int
     * } $params DNS plan parameters
     * @param bool $raw Return raw API response
     * @return array|bool Returns true when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function edit(int $planId, array $params, bool $raw = false): array|bool
    {
        try {
            // Validate required fields
            $required = ['plan_name', 'dnsserverid', 'maxdomains', 'maxdomainsrec', 'ttl'];
            foreach ($required as $field) {
                if (!isset($params[$field])) {
                    throw new VirtualizorApiException("{$field} is required");
                }
            }

            // Validate numeric fields
            foreach (['maxdomains', 'maxdomainsrec', 'ttl'] as $field) {
                if (!is_int($params[$field]) || $params[$field] <= 0) {
                    throw new VirtualizorApiException("{$field} must be a positive integer");
                }
            }

            $response = $this->api->editDnsPlan($planId, $params);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to edit DNS plan: Operation unsuccessful'
                );
            }

            return true;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                "Failed to edit DNS plan {$planId}: " . $e->getMessage(),
                $e->getContext()
            );
        }
    }

    /**
     * Delete DNS plan(s)
     *
     * @param int|array $planIds Single plan ID or array of plan IDs
     * @param bool $raw Return raw API response
     * @return array Returns deleted plan info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function delete(int|array $planIds, bool $raw = false): array
    {
        try {
            $response = $this->api->deleteDnsPlans($planIds);

            if ($raw) {
                return $response;
            }

            if (empty($response['done'])) {
                throw new VirtualizorApiException(
                    'Failed to delete DNS plan: Operation unsuccessful'
                );
            }

            // Format the deleted plan information
            $deletedPlans = [];
            foreach ($response['done'] as $id => $plan) {
                $deletedPlans[$id] = [
                    'id' => (int) $plan['dnsplid'],
                    'pdns_id' => (int) $plan['pdnsid'],
                    'name' => $plan['plan_name'],
                    'limits' => [
                        'max_domains' => (int) $plan['max_domains'],
                        'max_domain_records' => (int) $plan['max_domain_records'],
                        'default_ttl' => (int) $plan['def_ttl']
                    ]
                ];
            }

            return [
                'deleted_plans' => $deletedPlans,
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            $ids = is_array($planIds) ? implode(', ', $planIds) : $planIds;
            throw new VirtualizorApiException(
                "Failed to delete DNS plan(s) {$ids}: " . $e->getMessage(),
                $e->getContext()
            );
        }
    }
} 