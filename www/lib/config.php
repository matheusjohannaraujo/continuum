<?php

// Changing "php.ini" during execution

ini_set("default_charset", "utf-8");
ini_set("set_time_limit", "3600");
ini_set("max_execution_time", "3600");
ini_set("default_socket_timeout", "3600");
ini_set("max_input_time", "3600");
ini_set("max_input_vars", "6000");
ini_set("memory_limit", "6144M");
ini_set("post_max_size", "6144M");
ini_set("upload_max_filesize", "6144M");
ini_set("max_file_uploads", "200");
error_reporting(E_ALL ^ E_WARNING);
date_default_timezone_set(input_env("TIMEZONE"));

// CORS Enable
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$redis_conn = null;
try {
    $env = new \Lib\ENV;
    $env->read();
    $redis_host = $env->get("REDIS_HOST");
    $redis_port = $env->get("REDIS_PORT");
    $redis_username = $env->get("REDIS_USERNAME");
    $redis_password = $env->get("REDIS_PASSWORD");
    $redis_conn = new Predis\Client([
        'scheme' => 'tcp',
        'host' => $redis_host,
        'port' => $redis_port,
        'username' => $redis_username,
        'password' => $redis_password
    ]);
} catch (\Throwable $th) {
    create_log($th);
    $redis_conn = null;
}
$GLOBALS["redis_conn"] = $redis_conn;

\Lib\Meter::start();

!defined('CLI') ? session() : null;

!defined('CLI') ? \Lib\Route::init() : null;

require_once __DIR__ . "/db_conn_capsule.php";
