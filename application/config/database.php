<?php

$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
    'dsn'   => '',
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => 'root',
    'database' => 'ci_ee_db',
    'dbdriver' => 'mysqli',
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
    'options'  => array(
        // PDO options untuk koneksi yang lebih aman
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_PERSISTENT => FALSE,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
        // Untuk performa dan keamanan
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => TRUE,
        PDO::MYSQL_ATTR_LOCAL_INFILE => TRUE,
    )
);


return $db;