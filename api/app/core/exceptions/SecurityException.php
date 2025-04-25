<?php
namespace App\Core\Exceptions;

use Exception;

class SecurityException extends Exception
{
    protected $code = 403;

    public function __construct($message = "A secure connection is required (HTTPS).", $code = 403, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
