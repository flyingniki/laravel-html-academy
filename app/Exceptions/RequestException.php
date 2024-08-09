<?php

namespace App\Exceptions;

class RequestException extends \Exception
{
    public function getStatusCode()
    {
        return 400;
    }
}
