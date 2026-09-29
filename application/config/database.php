<?php

$active_group = 'default';
$query_builder = TRUE;

// Default group memakai SQLite via PDO supaya contoh project ini
// bisa langsung jalan tanpa server MySQL. Untuk MySQL, ubah 'dbdriver'
// ke 'pdo' + dsn mysql:..., atau aktifkan group 'pdo'/'mysqli' di bawah.
$db['default'] = array(
    'dsn'   => 'sqlite:' . __DIR__ . '/../../storage/database.sqlite',
    'hostname' => '',
    'username' => '',
    'password' => '',
    'database' => '',
    'dbdriver' => 'pdo',
    'subdriver' => 'sqlite',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);



$db['pdo'] = array(
    'dsn'   => 'mysql:host=localhost;dbname=ci_ee_db;charset=utf8',
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => 'root',
    'database' => 'ci_ee_db',
    'dbdriver' => 'pdo',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => FALSE,
    'compress' => FALSE,
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE,
    
    // Konfigurasi tambahan untuk PDO
    'port'     => '3306', // Port MySQL default
    // NOTE: constant PDO::MYSQL_ATTR_* only exists when pdo_mysql is loaded;
    // referencing it unconditionally fatals on hosts without the extension.
    'options'  => array_filter(array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_PERSISTENT => FALSE,
        defined('PDO::MYSQL_ATTR_INIT_COMMAND') ? PDO::MYSQL_ATTR_INIT_COMMAND : null => defined('PDO::MYSQL_ATTR_INIT_COMMAND') ? "SET NAMES 'utf8'" : null,
        defined('PDO::MYSQL_ATTR_USE_BUFFERED_QUERY') ? PDO::MYSQL_ATTR_USE_BUFFERED_QUERY : null => TRUE,
    ), fn($v) => $v !== null)
);


return $db;