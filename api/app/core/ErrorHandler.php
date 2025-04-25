<?php

/**
 * File: ErrorHandler.php
 * Created at: 2025-04-22 21:00:00
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
  private static string $msgResponse;
  private static int $code;
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
    $message = self::messageBuilder($exception);
    self::$logger->log($message);

    http_response_code(self::$code);
    $response = ['error' => self::$msgResponse];

    if (\Config\DEBUGMODE) {
        $response['details'] = [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ];
    }

    echo json_encode($response);
    exit();
  }

  private static function messageBuilder($exception)
  {
    self::$code = 500;
    self::$msgResponse = "Internal Server Error";

    if ($exception instanceof \App\Core\Exceptions\RequestException) {
      self::$code = $exception->getCode();
      self::$msgResponse = $exception->getMessage();
      return "Request error: " . $exception->getMessage();
    }
    
    if ($exception instanceof \App\Core\Exceptions\SecurityException) {
      self::$code = $exception->getCode();
      self::$msgResponse = $exception->getMessage();
      return "Connection error: " . $exception->getMessage();
    }

    if ($exception instanceof \PDOException) {
      self::$code = 503;
      self::$msgResponse = "Database error: " . $exception->getMessage();
      return "Database error: " . $exception->getMessage();
    }

    return "Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine();

  }
}
