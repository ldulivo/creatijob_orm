<?php

/**
 * File: Initialize.php
 * Created at: 2023-07-10 16:00:00
 * Author: LDulivo
 * -----
 * Description: 
 * This file initializes the core components of the application.
 * It configures logging, loads configuration files, and sets up error handling.
 * 
 * Details:
 * 1. Initializes loggers for API events and errors.
 * 2. Checks if the configuration file (Config.php) exists.
 * 3. Loads Config.php and sets error reporting based on the DEBUGMODE flag.
 * 4. Registers the error handler to log errors using the ErrorHandler class.
 * -----
 */

namespace App\Core;

use App\Core\Logger;
use Config;
use App\Core\ErrorHandler;


class Initialize
{
  function __construct()
  {
    $apiLogger = new Logger(API_PATH . '/log/api.log');
    $errorLogger = new Logger(API_PATH . '/log/error.log');

    /**
     * CONFIGURATION FILES.
     */

    $configFilePath = './Config.php';
    if (!file_exists($configFilePath)) {
      $errorLogger->log('Error: Config.php not found.');
      exit('Configuration not found. See error log.');
    }

    require_once($configFilePath);

    Config\DEBUGMODE === true
      ? error_reporting(E_ERROR | E_WARNING | E_PARSE)
      : error_reporting(0);

    /**
     * ERROR HANDLING
     */

    ErrorHandler::register($errorLogger);
  }
}
