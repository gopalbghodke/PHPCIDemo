<?php

// For sqlite database, we add database_path
$database = env('LINCKO_SAMPLE_DB_DATABASE');
if (env('LINCKO_SAMPLE_DB_DRIVER') == 'sqlite') {
    $database = database_path(env('LINCKO_SAMPLE_DB_DATABASE'));
}

return [

    // For shared database
    'brunoocto_sample' => [
        'driver' => !is_null(env('LINCKO_SAMPLE_DB_DRIVER')) ? env('LINCKO_SAMPLE_DB_DRIVER') : 'mysql',
        'url' => !is_null(env('LINCKO_SAMPLE_DATABASE_URL')) ? env('LINCKO_SAMPLE_DATABASE_URL') : env('DATABASE_URL'),
        'host' => !is_null(env('LINCKO_SAMPLE_DB_HOST')) ? env('LINCKO_SAMPLE_DB_HOST') : env('DB_HOST', '127.0.0.1'),
        'port' => !is_null(env('LINCKO_SAMPLE_DB_PORT')) ? env('LINCKO_SAMPLE_DB_PORT') : env('DB_PORT', '3306'),
        'database' => !is_null($database) ? $database : env('DB_DATABASE', 'forge'),
        'username' => !is_null(env('LINCKO_SAMPLE_DB_USERNAME')) ? env('LINCKO_SAMPLE_DB_USERNAME') : env('DB_USERNAME', 'forge'),
        'password' => !is_null(env('LINCKO_SAMPLE_DB_PASSWORD')) ? env('LINCKO_SAMPLE_DB_PASSWORD') : env('DB_PASSWORD', ''),
        'unix_socket' => !is_null(env('LINCKO_SAMPLE_DB_SOCKET')) ? env('LINCKO_SAMPLE_DB_SOCKET') : env('DB_SOCKET', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => 'sample_',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => null,
        'options' => extension_loaded('pdo_mysql') ? array_filter([
            PDO::MYSQL_ATTR_SSL_CA => !is_null(env('LINCKO_SAMPLE_MYSQL_ATTR_SSL_CA')) ? env('LINCKO_SAMPLE_MYSQL_ATTR_SSL_CA') : env('MYSQL_ATTR_SSL_CA'),
        ]) : [],
        'foreign_key_constraints' => !is_null(env('LINCKO_SAMPLE_DB_FOREIGN_KEYS')) ? env('LINCKO_SAMPLE_DB_FOREIGN_KEYS') : env('DB_FOREIGN_KEYS', false),
    ],

    // For database per server (read-only is default, or CRUD enable but per server)
    'brunoocto_sample_static' => [
        'driver' => 'sqlite',
        'url' => '',
        // For static database (Read-only), linked to the package
        'database' => __DIR__.'/../../database/database.sample.sqlite',
        // For dynamic database (CRUD) linked to the server itself (but not shared between multiple server instances)
        // 'database' => isset($_SERVER['HTTP_HOST']) ? database_path('lincko_sample_database-'.\Str::slug($_SERVER['HTTP_HOST']).'-'.\App::environment().'.sqlite') : 'lincko_sample_database-'.\App::environment().'.sqlite',
        'prefix' => 'sample_',
        'foreign_key_constraints' => !is_null(env('LINCKO_SAMPLE_DB_FOREIGN_KEYS')) ? env('LINCKO_SAMPLE_DB_FOREIGN_KEYS') : env('DB_FOREIGN_KEYS', false),
    ],

];
