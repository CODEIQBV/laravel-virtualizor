<?php

namespace CODEIQ\Virtualizor\Services;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Services\Admin\UserService;

/**
 * @method UserService users()
 */
class AdminServices
{
    protected AdminApi $api;

    protected ?UserService $userService = null;

    public function __construct(AdminApi $api)
    {
        $this->api = $api;
    }

    public function users(): UserService
    {
        if (! $this->userService) {
            $this->userService = new UserService($this->api);
        }

        return $this->userService;
    }
}
