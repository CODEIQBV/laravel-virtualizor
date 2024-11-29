<?php

namespace CODEIQ\Virtualizor\Services\Enduser;

use CODEIQ\Virtualizor\Api\EnduserApi;

class VpsManager
{
    public function __construct(
        private readonly EnduserApi $api
    ) {}

    public function list(array $search = [], int $page = 1, int $perPage = 50): array
    {
        return $this->api->listvs($page, $perPage, $search);
    }

    public function create(array $data): array
    {
        return $this->api->createVps($data);
    }
}
