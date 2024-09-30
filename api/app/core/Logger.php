<?php

/**
 * File: Logger.php
 * Created at: 2023-07-10 16:00:00
 * Author: LDulivo
 * -----
 * Description: 
 * This file defines the Logger class used for logging messages to a log file.
 * It supports creating and appending messages with timestamps to the specified log file.
 * 
 * Details:
 * 1. The constructor takes the log file path as an argument and sets it to the $logFile property.
 * 2. The log method formats the messages with timestamps and appends them to the log file.
 * 3. Checks if writing to the log file is successful and outputs an error message if it fails.
 * -----
 */

namespace App\Core;

class Logger
{
  protected $logFile;

  public function __construct($logFile)
  {
    $this->logFile = $logFile;
  }

  public function log($message)
  {
    $formattedMessage = '[ ' . date('Y-m-d H:i:s') . ' ] - ' . $message . PHP_EOL;
    $result = file_put_contents($this->logFile, $formattedMessage, FILE_APPEND);

    if ($result === false) {
        echo "Error: Failed to write to log file: {$this->logFile}" . PHP_EOL;
    }
}
}
?>
