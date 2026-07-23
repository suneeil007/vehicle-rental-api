<?php

namespace App\Exceptions;

use Exception;

abstract class ApiException extends Exception
{
    protected int $status = 500;

    public function __construct(
        string $message = ''
    ) {
        parent::__construct($message);
    }

    public function status(): int
    {
        return $this->status;
    }
}