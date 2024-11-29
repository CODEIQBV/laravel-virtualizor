<?php

namespace CODEIQ\Virtualizor;

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

class VirtualizorAdmin
{
    protected static ?AdminApi $api = null;

    protected static function getApi(): AdminApi
    {
        if (! self::$api) {
            $config = config('virtualizor');
            self::$api = new AdminApi(
                $config['admin']['key'],
                $config['admin']['pass'],
                $config['admin']['ip'],
                $config['admin']['port']
            );
        }

        return self::$api;
    }

    public static function users(): UserService
    {
        return new UserService(self::getApi());
    }

    public static function storage(): StorageManager
    {
        return new StorageManager(self::getApi());
    }

    public static function vps(): VpsManager
    {
        return new VpsManager(self::getApi());
    }

    public static function servers(): ServerManager
    {
        return new ServerManager(self::getApi());
    }

    public static function acl(): AclManager
    {
        return new AclManager(self::getApi());
    }

    public static function backup(): BackupManager
    {
        return new BackupManager(self::getApi());
    }

    public static function ippool(): IpPoolManager
    {
        return new IpPoolManager(self::getApi());
    }

    public static function serverGroups(): ServerGroupManager
    {
        return new ServerGroupManager(self::getApi());
    }

    public static function plans(): PlanManager
    {
        return new PlanManager(self::getApi());
    }

    public static function dns(): DnsManager
    {
        return new DnsManager(self::getApi());
    }

    public static function logs(): LogManager
    {
        return new LogManager(self::getApi());
    }

    public static function services(): ServiceManager
    {
        return new ServiceManager(self::getApi());
    }

    public static function vpsStats(): VpsStatisticsManager
    {
        return new VpsStatisticsManager(self::getApi());
    }
}
