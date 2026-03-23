<?php

namespace App\Exceptions;

use Exception;

class SupabaseException extends Exception
{
    protected int $statusCode;
    protected array $context;

    public function __construct(string $message = '', int $statusCode = 500, array $context = [], ?\Throwable $previous = null)
    {
        $this->statusCode = $statusCode;
        $this->context = $context;
        parent::__construct($message, $statusCode, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
