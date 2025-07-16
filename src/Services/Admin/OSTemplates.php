<?php

namespace CODEIQ\Virtualizor\Services\Admin;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;

class OSTemplates
{

    public function __construct(
        private readonly AdminApi $api
    ) {}

    public function oslists(): array
    {
        try {
            $response = $this->api->listOSTemplates();

            return $response;
        } catch (VirtualizorApiException $e) {
            throw new VirtualizorApiException(
                'Failed to list os lists: ' . $e->getMessage(),
                $e->getContext()
            );
        }
    }
} 