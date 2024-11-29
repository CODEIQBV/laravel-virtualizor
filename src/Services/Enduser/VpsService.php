<?php

namespace CODEIQ\Virtualizor\Services\Enduser;

use CODEIQ\Virtualizor\Api\EnduserApi;

class VpsService
{
    protected EnduserApi $api;

    public function __construct(EnduserApi $api)
    {
        $this->api = $api;
    }

    /**
     * List all VPS
     *
     * @param  array  $search  Search parameters
     * @param  int  $page  Page number
     * @param  int  $perPage  Records per page
     */
    public function list(array $search = [], int $page = 1, int $perPage = 50): array
    {
        return $this->api->listvs($page, $perPage, $search);
    }

    /**
     * Create a new VPS
     *
     * @param  array  $data  VPS creation data
     */
    public function create(array $data): array
    {
        return $this->api->createVps($data);
    }
}
