<?php

namespace CODEIQ\Virtualizor\Exceptions;

use Exception;

class VirtualizorApiException extends Exception
{
    protected array $context;

    public function __construct(string $message, array $context = [], int $code = 0)
    {
        parent::__construct($message, $code);
        $this->context = $context;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
