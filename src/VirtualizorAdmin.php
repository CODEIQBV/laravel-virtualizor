<?php

namespace CODEIQ\Virtualizor;

use CODEIQ\Virtualizor\Api\AdminApi;
use CODEIQ\Virtualizor\Services\Admin\AclManager;
use CODEIQ\Virtualizor\Services\Admin\BackupManager;
use CODEIQ\Virtualizor\Services\Admin\IpPoolManager;
use CODEIQ\Virtualizor\Services\Admin\ServerGroupManager;
use CODEIQ\Virtualizor\Services\Admin\ServerManager;
use CODEIQ\Virtualizor\Services\Admin\UserManager;
use CODEIQ\Virtualizor\Services\Admin\VpsManager;

class VirtualizorAdmin
{
    private static ?AdminApi $api = null;

    private static function getApi(): AdminApi
    {
        if (! self::$api) {
            $config = config('virtualizor.admin');
            self::$api = new AdminApi(
                $config['key'],
                $config['pass'],
                $config['ip'],
                $config['port']
            );
        }

        return self::$api;
    }

    public static function users(): UserManager
    {
        return new UserManager(self::getApi());
    }

    public static function vps(): VpsManager
    {
        return new VpsManager(self::getApi());
    }

    public static function acl(): AclManager
    {
        return new AclManager(self::getApi());
    }

    public static function servers(): ServerManager
    {
        return new ServerManager(self::getApi());
    }

    public static function serverGroups(): ServerGroupManager
    {
        return new ServerGroupManager(self::getApi());
    }

    public static function backup(): BackupManager
    {
        return new BackupManager(self::getApi());
    }

    public static function ipPool(): IpPoolManager
    {
        return new IpPoolManager(self::getApi());
    }
}
