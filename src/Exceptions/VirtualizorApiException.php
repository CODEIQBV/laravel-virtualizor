<?php

namespace CODEIQ\Virtualizor\Exceptions;

/**
 * Exception thrown when Virtualizor API requests fail
 */
class VirtualizorApiException extends \Exception implements \Throwable
{
    /**
     * Additional context information about the error
     */
    private array $context;

    /**
     * Create a new API exception
     *
     * @param string $message Error message
     * @param array $context Additional error context
     * @param int $code Error code
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message,
        array $context = [],
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    /**
     * Get additional error context
     *
     * @return array Error context information
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
