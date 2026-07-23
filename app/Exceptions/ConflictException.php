<?php

namespace App\Exceptions;

class ConflictException extends ApiException
{
    protected int $status = 409;
}