<?php

namespace CODEIQ\Virtualizor\Contracts;

use CODEIQ\Virtualizor\Services\AdminServices;
use CODEIQ\Virtualizor\Services\EnduserServices;

interface VirtualizorInterface
{
    public function admin(): AdminServices;

    public function enduser(): EnduserServices;
}
