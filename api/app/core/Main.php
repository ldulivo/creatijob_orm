<?php

/**
 * File: Main.php
 * Created at: 2023-07-10 16:00:00
 * Author: LDulivo
 * -----
 * Description: 
 * This file is the main entry point of the application.
 * It sets up the path constants, loads the autoloader,
 * initializes the main components, and runs the application.
 * 
 * Details:
 * 1. Defines the API_PATH and WEB_ROOT constants based on the directory structure.
 * 2. Requires the Autoloader.php file to dynamically load the classes.
 * 3. Initializes the system using the Initialize class.
 * 4. Sets up HTTP headers with the Header class.
 * 5. Starts the application processing with App::start().
 * 6. Loads route files using ReadRouteFiles.
 * 7. Runs the application with App::run().
 * -----
 */

namespace App\Core;

$dir = __DIR__;
$dirSplit = explode('/', $dir);

define('API_PATH', substr($dir, 0, strlen('/' . $dirSplit[count($dirSplit) - 2] . '/' . $dirSplit[count($dirSplit) - 1]) * -1) );
define('WEB_ROOT', substr(API_PATH, 0, strlen('/' . $dirSplit[count($dirSplit) - 3]) * -1) );

require_once(API_PATH . '/app/core/Autoloader.php');

use App\Core\Initialize;
use App\Core\Header;
use App\Core\ReadRouteFiles;

new Initialize();

$header = new Header();
$header->accessControl();
App::start();

ReadRouteFiles::getFiles();

App::run();
return;


