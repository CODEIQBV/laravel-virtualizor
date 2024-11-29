<?php

namespace CODEIQ\Virtualizor\Facades;

use Illuminate\Support\Facades\Facade;
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

/**
 * @method static UserService users()
 * @method static StorageManager storage()
 * @method static VpsManager vps()
 * @method static ServerManager servers()
 * @method static AclManager acl()
 * @method static BackupManager backup()
 * @method static IpPoolManager ippool()
 * @method static ServerGroupManager serverGroups()
 * @method static PlanManager plans()
 * @method static DnsManager dns()
 * @method static LogManager logs()
 * @method static ServiceManager services()
 * 
 * @see \CODEIQ\Virtualizor\Services\AdminServices
 */
class VirtualizorAdmin extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'virtualizor.admin';
    }
} 