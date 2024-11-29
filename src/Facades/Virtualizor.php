<?php

namespace CODEIQ\Virtualizor\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \CODEIQ\Virtualizor\Virtualizor
 */
class Virtualizor extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'virtualizor';
    }
}
