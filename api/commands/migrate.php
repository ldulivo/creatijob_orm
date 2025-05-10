<?php

switch (Config\DB_DRIVER) {
    case 'mysql':
        require ROOT_PATH . '/app/db/heart/mysql/migration/_migrate.php';
        break;

    default:
        throw new \Exception("Driver no soportado");
}
