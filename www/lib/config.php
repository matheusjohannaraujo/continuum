<?php

// Changing "php.ini" during execution

ini_set("default_charset", "utf-8");
ini_set("set_time_limit", "36000");
ini_set("max_execution_time", "36000");
ini_set("default_socket_timeout", "36000");
ini_set("max_input_time", "36000");
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

try {
    $env = new \Lib\ENV;
    $env->read();
    $redis_host = $env->get("REDIS_HOST");
    $redis_port = $env->get("REDIS_PORT");    
    $redis_username = $env->get("REDIS_USERNAME");
    $redis_password = $env->get("REDIS_PASSWORD");
    $redis_scheme = $env->get("REDIS_SCHEME");
    $redis_read_write_timeout = $env->get("REDIS_READ_WRITE_TIMEOUT");
    \Lib\SimpleRedis::config($redis_host, $redis_port, $redis_password, $redis_username, $redis_scheme, $redis_read_write_timeout);
} catch (\Throwable $th) {
    log_create($th);
}

try {
    $env = new \Lib\ENV;
    $env->read();
    $rabbitmq_host = $env->get("RABBITMQ_HOST");
    $rabbitmq_port = $env->get("RABBITMQ_PORT");
    $rabbitmq_username = $env->get("RABBITMQ_USERNAME");
    $rabbitmq_password = $env->get("RABBITMQ_PASSWORD");
    \Lib\SimpleRabbitMQ::config($rabbitmq_host, $rabbitmq_port, $rabbitmq_username, $rabbitmq_password);
} catch (\Throwable $th) {
    log_create($th);
}

\Lib\Meter::start();

!defined('CLI') ? session() : null;

!defined('CLI') ? \Lib\Route::init() : null;

require_once __DIR__ . "/db_conn_capsule.php";
