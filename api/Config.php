<?php
namespace Config;

// Load environment variables and keys from env.php
$envFilePath = __DIR__ . '/env.php';
if (!file_exists($envFilePath) && file_exists(dirname(__DIR__) . '/env.php')) {
  $envFilePath = dirname(__DIR__) . '/env.php';
}

if (!file_exists($envFilePath)) {
  exit("Error: 'env.php' file not found. Please create it based on 'env.example.php'.");
}

require_once $envFilePath;

/**
 * DEBUG MODE
 * -----
 * Description:
 * Constants related to debugging configuration.
 * 
 * Constants:
 * - DEBUGMODE: Indicates if debugging mode is enabled. When set to true, debugging is enabled.
 * -----
 */
const DEBUGMODE = true;

/**
 * DEVELOPMENT MODE
 * -----
 * Description:
 * Constants related to development mode.
 * 
 * Constants:
 * - DEVELOPMENT_MODE: Indicates if development mode is enabled. When set to true, development mode is enabled.
 * -----
 */
const DEVELOPMENT_MODE = true;

/**
 * CONFIGURATION FOR TOKENS
 * -----
 * Description:
 * Constants related to token generation and validation.
 * 
 * Constants:
 * - SECRET_KEY: The secret key used for generating and validating tokens.
 * - TOKEN_EXPIRATION_TIME: The lifetime of a token in seconds. The default is 3600 seconds (1 hour).
 * -----
 */
const SECRET_KEY = \SECRET_KEY;
const TOKEN_EXPIRATION_TIME = 3600;

/**
 * DOMAIN NAME
 * -----
 * Description:
 * Constants for the domain configuration.
 * 
 * Constants:
 * - DOMAIN_NAME: The name of the server domain.
 * - DOMAIN_PORT: The port on which the server is running.
 * -----
 */
const DOMAIN_NAME = 'localhost';
const DOMAIN_PORT = 80;

/**
 * CORS CONFIGURATION
 * -----
 * Description:
 * Constants related to Cross-Origin Resource Sharing.
 * 
 * Constants:
 * - ALLOWED_ORIGINS: List of allowed origins for CORS.
 * - ALLOW_CREDENTIALS: Whether to allow credentials (cookies, auth headers).
 * - ALLOWED_METHODS: HTTP methods allowed for CORS.
 * - ALLOWED_HEADERS: HTTP headers allowed for CORS.
 * - MAX_AGE: How long the results of a preflight request can be cached (in seconds).
 * -----
 */
const ALLOWED_ORIGINS = [
  'http://localhost:5500',
  'http://localhost',
  'https://creatijob.com',
];
const ALLOW_CREDENTIALS = true;
const ALLOWED_METHODS = 'OPTIONS, GET, POST, PUT, DELETE';
const ALLOWED_HEADERS = 'Origin, X-Requested-With, Content-Type, Accept, Authorization, xtoken, x-token';
const MAX_AGE = 0; // 86400

/**
 * DATABASE CONFIGURATION
 * -----
 * Description:
 * Constants for the database configuration.
 * 
 * Constants:
 * - DB_DRIVER: The database engine in use ('mysql' or 'sqlsrv').
 * 
 * MySQL-specific Constants:
 * - MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DBNAME, MYSQL_CHARSET
 * 
 * SQL Server-specific Constants:
 * - SQLSRV_HOST, SQLSRV_USERNAME, SQLSRV_PASSWORD, SQLSRV_DBNAME
 * -----
 */

// Choose the current DB driver: 'mysql' or 'sqlsrv' or 'pgsql'
const DB_DRIVER = 'mysql';

// MySQL configuration
const MYSQL_HOST     = \MYSQL_HOST;     // MySQL server host address
const MYSQL_USERNAME = \MYSQL_USERNAME; // Database username
const MYSQL_PASSWORD = \MYSQL_PASSWORD; // Database user password
const MYSQL_DBNAME   = \MYSQL_DBNAME;   // Name of the database
const MYSQL_CHARSET  = \MYSQL_CHARSET;  // Charset used for the connection (recommended: utf8mb4)
const MYSQL_PORT     = \MYSQL_PORT;     // MySQL port (default: 3306)

// SQL Server configuration
const SQLSRV_HOST     = \SQLSRV_HOST;     // SQL Server host address
const SQLSRV_USERNAME = \SQLSRV_USERNAME; // Database username
const SQLSRV_PASSWORD = \SQLSRV_PASSWORD; // Database user password
const SQLSRV_DBNAME   = \SQLSRV_DBNAME;   // Name of the database
const SQLSRV_PORT     = \SQLSRV_PORT;     // SQL Server port (default: 1433)
?>