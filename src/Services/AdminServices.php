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
use CODEIQ\Virtualizor\Services\Admin\ServiceManager;
use CODEIQ\Virtualizor\Services\Admin\VpsStatisticsManager;
use CODEIQ\Virtualizor\Services\Admin\ProcessManager;
use CODEIQ\Virtualizor\Services\Admin\DiskManager;
use CODEIQ\Virtualizor\Services\Admin\BandwidthManager;
use CODEIQ\Virtualizor\Services\Admin\FirewallManager;
use CODEIQ\Virtualizor\Services\Admin\ServerMonitoringManager;

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
 * @method ServiceManager services()
 * @method VpsStatisticsManager vpsStats()
 * @method ProcessManager processes()
 * @method DiskManager disk()
 * @method BandwidthManager bandwidth()
 * @method FirewallManager firewall()
 * @method ServerMonitoringManager monitoring()
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
    protected ?ServiceManager $serviceManager = null;
    protected ?VpsStatisticsManager $vpsStatsManager = null;
    protected ?ProcessManager $processManager = null;
    protected ?DiskManager $diskManager = null;
    protected ?BandwidthManager $bandwidthManager = null;
    protected ?FirewallManager $firewallManager = null;
    protected ?ServerMonitoringManager $serverMonitoringManager = null;

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

    public function services(): ServiceManager
    {
        if (! $this->serviceManager) {
            $this->serviceManager = new ServiceManager($this->api);
        }
        return $this->serviceManager;
    }

    public function vpsStats(): VpsStatisticsManager
    {
        if (! $this->vpsStatsManager) {
            $this->vpsStatsManager = new VpsStatisticsManager($this->api);
        }
        return $this->vpsStatsManager;
    }

    public function processes(): ProcessManager
    {
        if (! $this->processManager) {
            $this->processManager = new ProcessManager($this->api);
        }
        return $this->processManager;
    }

    public function disk(): DiskManager
    {
        if (! $this->diskManager) {
            $this->diskManager = new DiskManager($this->api);
        }
        return $this->diskManager;
    }

    public function bandwidth(): BandwidthManager
    {
        if (! $this->bandwidthManager) {
            $this->bandwidthManager = new BandwidthManager($this->api);
        }
        return $this->bandwidthManager;
    }

    public function firewall(): FirewallManager
    {
        if (! $this->firewallManager) {
            $this->firewallManager = new FirewallManager($this->api);
        }
        return $this->firewallManager;
    }

    public function monitoring(): ServerMonitoringManager
    {
        if (! $this->serverMonitoringManager) {
            $this->serverMonitoringManager = new ServerMonitoringManager($this->api);
        }
        return $this->serverMonitoringManager;
    }
}
