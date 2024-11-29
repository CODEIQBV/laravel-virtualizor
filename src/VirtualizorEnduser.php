<?php

namespace CODEIQ\Virtualizor;

use CODEIQ\Virtualizor\Api\EnduserApi;
use CODEIQ\Virtualizor\Services\Enduser\UserManager;
use CODEIQ\Virtualizor\Services\Enduser\VpsManager;

class VirtualizorEnduser
{
    private static ?EnduserApi $api = null;

    private static function getApi(): EnduserApi
    {
        if (! self::$api) {
            $config = config('virtualizor.enduser');
            self::$api = new EnduserApi(
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
}
