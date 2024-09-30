<?php

/**
 * File: Autoloader.php
 * Created at: 2023-07-10 16:00:00
 * Author: LDulivo
 * -----
 * Description: 
 * This file defines the autoloader function for the application.
 * It automatically loads PHP class files based on the class name 
 * and namespace following the PSR-4 standard.
 * 
 * Details:
 * 1. Registers an anonymous function with spl_autoload_register to automatically load classes.
 * 2. Splits the fully qualified class name by namespace separator and constructs the file path.
 * 3. Constructs the file path by converting namespace parts to lowercase directories 
 *    and appending '.php' to the class name.
 * 4. Checks if the constructed file path exists in the defined API_PATH and requires the file if it does.
 * 5. Returns true if the file is successfully loaded, otherwise false.
 * -----
 */

spl_autoload_register(function ($class) {
  $split = explode('\\', $class);
  $file = '';
  foreach ($split as $i => $part) {
    $file .= '/' . (count($split) === $i + 1 ? $part . '.php' : strtolower($part));
  }

  $file = API_PATH . $file;
  if (file_exists($file)) {
    require_once($file);
    return true;
  }

  return false;
});
