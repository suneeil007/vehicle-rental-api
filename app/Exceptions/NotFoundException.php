<?php

namespace App\Exceptions;

class NotFoundException extends ApiException
{
    protected int $status = 404;
}