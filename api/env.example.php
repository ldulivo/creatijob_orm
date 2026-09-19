<?php
/**
 * ARCHIVO DE EJEMPLO DE CONFIGURACIÓN DE ENTORNO Y CLAVES
 * -----
 * Descripción:
 * Plantilla de constantes y credenciales requeridas por la aplicación.
 * Copia este archivo como 'env.php' y completa los valores correspondientes a tu entorno.
 * -----
 */

// Clave secreta para generación y validación de tokens JWT
const SECRET_KEY = 'tu_clave_secreta_jwt_aqui';

// Configuración de MySQL
const MYSQL_HOST     = '127.0.0.1';        // Dirección del servidor MySQL
const MYSQL_USERNAME = 'tu_usuario_mysql'; // Usuario de la base de datos
const MYSQL_PASSWORD = 'tu_password_mysql';// Contraseña del usuario
const MYSQL_DBNAME   = 'nombre_bd';        // Nombre de la base de datos
const MYSQL_CHARSET  = 'utf8mb4';          // Charset para la conexión (recomendado: utf8mb4)
const MYSQL_PORT     = '3306';             // Puerto de MySQL (por defecto: 3306)

// Configuración de SQL Server
const SQLSRV_HOST     = '127.0.0.1';       // Dirección del servidor SQL Server
const SQLSRV_USERNAME = 'sa';              // Usuario de la base de datos
const SQLSRV_PASSWORD = 'yourPassword';    // Contraseña del usuario
const SQLSRV_DBNAME   = 'testDB';          // Nombre de la base de datos
const SQLSRV_PORT     = '1433';            // Puerto de SQL Server (por defecto: 1433)

