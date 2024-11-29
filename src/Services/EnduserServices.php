<?php

namespace CODEIQ\Virtualizor\Services;

use CODEIQ\Virtualizor\Api\EnduserApi;
use CODEIQ\Virtualizor\Services\Enduser\UserService;
use CODEIQ\Virtualizor\Services\Enduser\VpsService;

/**
 * @method UserService users()
 * @method VpsService vps()
 */
class EnduserServices
{
    protected EnduserApi $api;

    protected ?UserService $userService = null;

    protected ?VpsService $vpsService = null;

    public function __construct(EnduserApi $api)
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

    public function vps(): VpsService
    {
        if (! $this->vpsService) {
            $this->vpsService = new VpsService($this->api);
        }

        return $this->vpsService;
    }
}
