<?php
namespace App\Core\Exceptions;

use Exception;

class RequestException extends Exception
{
    protected $code = 400;

    public function __construct($message = "Invalid or unsupported HTTP request.", $code = 400, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
