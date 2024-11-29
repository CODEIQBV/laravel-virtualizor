<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

class BandwidthManager
{
    public function __construct(
        private readonly AdminApi $api
    ) {}

    /**
     * Get bandwidth usage
     *
     * @param string $month Month in YYYYMM format
     * @param bool $raw Return raw API response
     * @return array Returns formatted bandwidth info when raw is false, full response when raw is true
     * @throws VirtualizorApiException
     */
    public function usage(string $month, bool $raw = false): array
    {
        try {
            // Validate month format
            if (!preg_match('/^\d{6}$/', $month)) {
                throw new VirtualizorApiException('Month must be in YYYYMM format (e.g. 202401)');
            }

            $response = $this->api->getBandwidth($month);

            if ($raw) {
                return $response;
            }

            return [
                'bandwidth' => [
                    'total' => [
                        'limit' => [
                            'mb' => (float) $response['bandwidth']['limit'],
                            'gb' => (float) $response['bandwidth']['limit_gb']
                        ],
                        'used' => [
                            'mb' => (float) $response['bandwidth']['used'],
                            'gb' => (float) $response['bandwidth']['used_gb']
                        ],
                        'free' => [
                            'mb' => (float) $response['bandwidth']['free'],
                            'gb' => (float) $response['bandwidth']['free_gb']
                        ],
                        'percent' => [
                            'used' => (float) $response['bandwidth']['percent'],
                            'free' => (float) $response['bandwidth']['percent_free']
                        ]
                    ],
                    'daily_usage' => $response['bandwidth']['usage'] ?? [],
                    'incoming' => [
                        'usage' => $response['bandwidth']['in']['usage'] ?? [],
                        'total' => [
                            'limit' => [
                                'mb' => (float) $response['bandwidth']['in']['limit'],
                                'gb' => (float) $response['bandwidth']['in']['limit_gb']
                            ],
                            'used' => [
                                'mb' => (float) $response['bandwidth']['in']['used'],
                                'gb' => (float) $response['bandwidth']['in']['used_gb']
                            ],
                            'free' => [
                                'mb' => (float) $response['bandwidth']['in']['free'],
                                'gb' => (float) $response['bandwidth']['in']['free_gb']
                            ],
                            'percent' => [
                                'used' => (float) $response['bandwidth']['in']['percent'],
                                'free' => (float) $response['bandwidth']['in']['percent_free']
                            ]
                        ]
                    ],
                    'outgoing' => [
                        'usage' => $response['bandwidth']['out']['usage'] ?? [],
                        'total' => [
                            'limit' => [
                                'mb' => (float) $response['bandwidth']['out']['limit'],
                                'gb' => (float) $response['bandwidth']['out']['limit_gb']
                            ],
                            'used' => [
                                'mb' => (float) $response['bandwidth']['out']['used'],
                                'gb' => (float) $response['bandwidth']['out']['used_gb']
                            ],
                            'free' => [
                                'mb' => (float) $response['bandwidth']['out']['free'],
                                'gb' => (float) $response['bandwidth']['out']['free_gb']
                            ],
                            'percent' => [
                                'used' => (float) $response['bandwidth']['out']['percent'],
                                'free' => (float) $response['bandwidth']['out']['percent_free']
                            ]
                        ]
                    ]
                ],
                'month' => $response['month'] ?? null,
                'timestamp' => $response['timenow'] ?? null,
                'time_taken' => $response['time_taken'] ?? null
            ];
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to get bandwidth usage: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }
} 