<?php
namespace Config;

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
const SECRET_KEY = 'S<}2<Pz!8c@[5IC&4H@NJcCKAbanJtRGpO(KuY71M}SjM]uNgsu^XM$_}#l1e94A';
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
const DOMAIN_PORT = 8080;

/**
 * DATABASE CONFIGURATION
 * -----
 * Description:
 * Constants for the database configuration.
 * 
 * Constants:
 * - DB_HOST: The hostname for the database server.
 * - DB_USERNAME: The username to connect to the database.
 * - DB_PASSWORD: The password to connect to the database.
 * - DB_NAME: The name of the database to connect to.
 * - DB_CHARSET: The character set used by the database.
 * -----
 */
const DB_HOST = '127.0.0.1';
const DB_USERNAME = 'root';
const DB_PASSWORD = '123ASD';
const DB_NAME = 'test';
const DB_CHARSET = 'utf8mb4';
?>