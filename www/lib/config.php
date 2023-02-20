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

$client = null;
try {
    $env = new \Lib\ENV;
    $env->read();
    $redis_host = $env->get("REDIS_HOST");
    $redis_port = $env->get("REDIS_PORT");
    $client = new Predis\Client([
        'scheme' => 'tcp',
        'host' => $redis_host,
        'port' => $redis_port,
    ]);
} catch (\Throwable $th) {
    //throw $th;
    $client = null;
}
$GLOBALS["redis_conn"] = $client;

\Lib\Meter::start();

!defined('CLI') ? session() : null;

!defined('CLI') ? \Lib\Route::init() : null;

require_once __DIR__ . "/db_conn_capsule.php";
