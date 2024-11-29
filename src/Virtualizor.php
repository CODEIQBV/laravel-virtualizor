<?php

namespace CODEIQ\Virtualizor;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Api\EnduserApi;
use CODEIQ\Virtualizor\Contracts\VirtualizorInterface;
use CODEIQ\Virtualizor\Exceptions\VirtualizorApiException;
use CODEIQ\Virtualizor\Services\AdminServices;
use CODEIQ\Virtualizor\Services\EnduserServices;

/**
 * Virtualizor API Client
 *
 * @method AdminServices admin() Access admin API services
 * @method EnduserServices enduser() Access enduser API services
 *
 * @property-read AdminServices $admin
 * @property-read EnduserServices $enduser
 *
 * @template TService
 *
 * @method TService service(string $name) Get a specific service instance
 */
class Virtualizor implements VirtualizorInterface
{
    protected ?AdminApi $adminApi = null;

    protected ?EnduserApi $enduserApi = null;

    protected ?AdminServices $adminServices = null;

    protected ?EnduserServices $enduserServices = null;

    public function __construct(
        private readonly array $config
    ) {}

    public function admin(): AdminServices
    {
        if (! $this->config['admin']['enabled']) {
            throw new VirtualizorApiException('Virtualizor Admin API is not enabled in configuration.');
        }

        if (! $this->adminServices) {
            if (! $this->adminApi) {
                $this->adminApi = new AdminApi(
                    $this->config['admin']['key'],
                    $this->config['admin']['pass'],
                    $this->config['admin']['ip'],
                    $this->config['admin']['port']
                );
            }
            $this->adminServices = new AdminServices($this->adminApi);
        }

        return $this->adminServices;
    }

    public function enduser(): EnduserServices
    {
        if (! $this->config['enduser']['enabled']) {
            throw new VirtualizorApiException('Virtualizor Enduser API is not enabled in configuration.');
        }

        if (! $this->enduserServices) {
            if (! $this->enduserApi) {
                $this->enduserApi = new EnduserApi(
                    $this->config['enduser']['key'],
                    $this->config['enduser']['pass'],
                    $this->config['enduser']['ip'],
                    $this->config['enduser']['port']
                );
            }
            $this->enduserServices = new EnduserServices($this->enduserApi);
        }

        return $this->enduserServices;
    }

    /**
     * Magic method to allow property access for services
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        return match ($name) {
            'admin' => $this->admin(),
            'enduser' => $this->enduser(),
            default => throw new VirtualizorApiException("Unknown service: $name")
        };
    }
}
