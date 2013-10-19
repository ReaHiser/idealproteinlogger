<?php

//----------------------------
// DATABASE CONFIGURATION
//----------------------------

/*

Valid types (adapters) are Postgres & MySQL:

'type' must be one of: 'pgsql' or 'mysql'

*/

$config = include_once 'config/config.php';

return array(
    'db' => array(
        'development' => array(
            'type'      => 'mysql',
            'host'      => $config['db']['host'],
            'port'      => 3306,
            'database'  => $config['db']['dbname'],
            'user'      => $config['db']['user'],
            'password'  => $config['db']['password'],
            'directory' => '/',
            //'socket' => '/var/run/mysqld/mysqld.sock'
        ),
    ),
    'migrations_dir' => RUCKUSING_WORKING_BASE . DIRECTORY_SEPARATOR . 'migrations',
    'db_dir' => RUCKUSING_WORKING_BASE . DIRECTORY_SEPARATOR . 'db',
    'log_dir' => RUCKUSING_WORKING_BASE . DIRECTORY_SEPARATOR . 'logs',
    'ruckusing_base' => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vendor/ruckusing/ruckusing-migrations'
);
