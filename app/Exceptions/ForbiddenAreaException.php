<?php

namespace App\Exceptions;

use Exception;

class ForbiddenAreaException extends Exception
{
    protected int $statusCode;

    public function __construct(string $message, int $statusCode)
    {
        parent::__construct($message, $statusCode);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
