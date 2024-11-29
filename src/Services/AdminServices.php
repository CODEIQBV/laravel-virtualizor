<?php

namespace CODEIQ\Virtualizor\Services;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Services\Admin\AclManager;
use CODEIQ\Virtualizor\Services\Admin\BackupManager;
use CODEIQ\Virtualizor\Services\Admin\IpPoolManager;
use CODEIQ\Virtualizor\Services\Admin\ServerGroupManager;
use CODEIQ\Virtualizor\Services\Admin\StorageManager;
use CODEIQ\Virtualizor\Services\Admin\UserService;
use CODEIQ\Virtualizor\Services\Admin\VpsManager;
use CODEIQ\Virtualizor\Services\Admin\ServerManager;
use CODEIQ\Virtualizor\Services\Admin\PlanManager;
use CODEIQ\Virtualizor\Services\Admin\DnsManager;
use CODEIQ\Virtualizor\Services\Admin\LogManager;

/**
 * @method UserService users()
 * @method StorageManager storage()
 * @method VpsManager vps()
 * @method ServerManager servers()
 * @method AclManager acl()
 * @method BackupManager backup()
 * @method IpPoolManager ippool()
 * @method ServerGroupManager serverGroups()
 * @method PlanManager plans()
 * @method DnsManager dns()
 * @method LogManager logs()
 */
class AdminServices
{
    protected AdminApi $api;

    protected ?UserService $userService = null;
    protected ?StorageManager $storage = null;
    protected ?VpsManager $vpsManager = null;
    protected ?ServerManager $serverManager = null;
    protected ?AclManager $aclManager = null;
    protected ?BackupManager $backupManager = null;
    protected ?IpPoolManager $ipPoolManager = null;
    protected ?ServerGroupManager $serverGroupManager = null;
    protected ?PlanManager $planManager = null;
    protected ?DnsManager $dnsManager = null;
    protected ?LogManager $logManager = null;

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

    public function storage(): StorageManager
    {
        if (! $this->storage) {
            $this->storage = new StorageManager($this->api);
        }

        return $this->storage;
    }

    public function vps(): VpsManager
    {
        if (! $this->vpsManager) {
            $this->vpsManager = new VpsManager($this->api);
        }
        return $this->vpsManager;
    }

    public function servers(): ServerManager
    {
        if (! $this->serverManager) {
            $this->serverManager = new ServerManager($this->api);
        }
        return $this->serverManager;
    }

    public function acl(): AclManager
    {
        if (! $this->aclManager) {
            $this->aclManager = new AclManager($this->api);
        }
        return $this->aclManager;
    }

    public function backup(): BackupManager
    {
        if (! $this->backupManager) {
            $this->backupManager = new BackupManager($this->api);
        }
        return $this->backupManager;
    }

    public function ippool(): IpPoolManager
    {
        if (! $this->ipPoolManager) {
            $this->ipPoolManager = new IpPoolManager($this->api);
        }
        return $this->ipPoolManager;
    }

    public function serverGroups(): ServerGroupManager
    {
        if (! $this->serverGroupManager) {
            $this->serverGroupManager = new ServerGroupManager($this->api);
        }
        return $this->serverGroupManager;
    }

    public function plans(): PlanManager
    {
        if (! $this->planManager) {
            $this->planManager = new PlanManager($this->api);
        }
        return $this->planManager;
    }

    public function dns(): DnsManager
    {
        if (! $this->dnsManager) {
            $this->dnsManager = new DnsManager($this->api);
        }
        return $this->dnsManager;
    }

    public function logs(): LogManager
    {
        if (! $this->logManager) {
            $this->logManager = new LogManager($this->api);
        }
        return $this->logManager;
    }
}
