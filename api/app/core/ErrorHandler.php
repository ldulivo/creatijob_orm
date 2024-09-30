<?php

/**
 * File: ErrorHandler.php
 * Created at: 2023-07-10 16:00:00
 * Author: LDulivo
 * -----
 * Description: 
 * This file defines the ErrorHandler class which is responsible for handling errors and exceptions.
 * It logs errors and exceptions and provides a user-friendly response.
 * 
 * Details:
 * 1. The register method registers custom error and exception handlers.
 * 2. The handleError method handles PHP errors, logs them, and throws an ErrorException.
 * 3. The handleException method handles uncaught exceptions, logs them, and sends a generic error response to the user.
 * -----
 */

namespace App\Core;

class ErrorHandler
{
  protected static $logger;

  public static function register($logger)
  {
    self::$logger = $logger;
    set_error_handler([self::class, 'handleError']);
    set_exception_handler([self::class, 'handleException']);
  }

  public static function handleError($errno, $errstr, $errfile, $errline)
  {
    $message = "Error [{$errno}]: {$errstr} in {$errfile} on line {$errline}";
    self::$logger->log($message);
    throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
  }

  public static function handleException($exception)
  {
    $message = "Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine();
    self::$logger->log($message);

    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error']);
    exit();
  }
}
