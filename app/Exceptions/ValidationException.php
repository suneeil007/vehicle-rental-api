<?php

namespace App\Exceptions;

class ValidationException extends ApiException
{
    protected int $status = 422;
}