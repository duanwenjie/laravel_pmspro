<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => 'mysql',

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => "$[DB_HOST]$",
            'port' => '3306',
            'database' => "pmspro",
            'username' => 'pmspro',
            'password' => "$[DB_PASSWD]$",
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        // OMS测试数据库
        'oms' => [
            'driver' => 'mysql',
            'host' => "$[OMS_SYSTEM_DB_HOST]$",
            'port' => '3306',
            'database' => "oms_eb",
            'username' => 'pmspro',
            'password' => "$[OMS_SYSTEM_DB_PASSWD]$",
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'oms_sf' => [
            'driver' => 'mysql',
            'host' => "$[OMS_SF_SYSTEM_DB_HOST]$",
            'port' => '3306',
            'database' => "oms_sf",
            'username' => 'pmspro',
            'password' => "$[OMS_SF_SYSTEM_DB_PASSWD]$",
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        // OMS数据库从库
        'oms_ck' => [
            'driver' => 'mysql',
            'host' => "$[OMS_SYSTEM_CK_DB_HOST]$",
            'port' => '3306',
            'database' => "oms_eb",
            'username' => 'pmspro',
            'password' => "$[OMS_SYSTEM_CK_DB_PASSWD]$",
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'oms_sf_ck' => [
            'driver' => 'mysql',
            'host' => "$[OMS_SF_SYSTEM_CK_DB_HOST]$",
            'port' => '3306',
            'database' => "oms_sf",
            'username' => 'pmspro',
            'password' => "$[OMS_SF_SYSTEM_CK_DB_PASSWD]$",
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        // PMS测试库
        'pms' =>[
            'driver' => 'mysql',
            'host' => "$[PMS_SYSTEM_DB_HOST]$",
            'port' => '3306',
            'database' => "ykspms",
            'username' => 'pmspro',
            'password' => "$[PMS_SYSTEM_DB_PASSWD]$",
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        // 进销存测试库
        'hz' => [
            'driver' => 'mysql',
            'host' => "$[HZ_SYSTEM_DB_HOST]$",
            'port' => '3306',
            'database' => "newerp_hz",
            'username' => 'pmspro',
            'password' => "$[HZ_SYSTEM_DB_PASSWD]$",
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        // 新基础资料测试库
        'sku_manage' => [
            'driver' => 'mysql',
            'host' => "$[SKUMANAGE_SYSTEM_DB_HOST]$",
            'port' => '3306',
            'database' => "skumanage",
            'username' => 'pmspro',
            'password' => "$[SKUMANAGE_SYSTEM_DB_PASSWD]$",
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        // koko测试库
        'yf' => [
            'driver' => 'mysql',
            'host' => "$[YF_SYSTEM_DB_HOST]$",
            'port' => '3306',
            'database' => "accountsystem",
            'username' => 'pmspro',
            'password' => "$[YF_SYSTEM_DB_PASSWD]$",
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        // 老基础资料测试库
        'sku' => [
            'driver' => 'mysql',
            'host' => "$[SKU_SYSTEM_DB_HOST]$",
            'port' => '3306',
            'database' => "skusystem",
            'username' => 'pmspro',
            'password' => "$[SKU_SYSTEM_DB_PASSWD]$",
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [
        'client' => env('REDIS_CLIENT', 'phpredis'),
        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],
        'default' => [
            'url' => env('REDIS_URL'),
            'host' => "$[REDIS_HOST]$",
            'password' => null,
            'port' => '6379',
            'database' => 0,
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => "$[REDIS_HOST]$",
            'password' => null,
            'port' =>'6379',
            'database' => 1,
        ],

    ],

];
