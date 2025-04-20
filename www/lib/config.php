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

function resolve(string $className, array $args = [], ?string $method = null)
{
    $reflection = new \ReflectionClass($className);

    if (is_null($method)) {
        // Injeção via construtor
        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            return new $className();
        }

        // Injeção direta de argumentos no construtor
        return $reflection->newInstanceArgs($args);
    }

    // Injeção via método
    if (!$reflection->hasMethod($method)) {
        throw new \Exception("Método '{$method}' não encontrado em '{$className}'.");
    }

    // Instancia a classe sem argumentos
    $instance = $reflection->newInstance();
    $methodReflection = $reflection->getMethod($method);
    $methodReflection->invokeArgs($instance, $args);
    return $instance;
}

try {
    $env = new \Lib\ENV;
    $env->read();
    $redis_host             = $env->get("REDIS_HOST", "localhost");
    $redis_port             = $env->get("REDIS_PORT", 6379);
    $redis_username         = $env->get("REDIS_USERNAME", "");
    $redis_password         = $env->get("REDIS_PASSWORD", "password");
    $redis_scheme           = $env->get("REDIS_SCHEME", "tcp");
    $redis_read_write_timeout = $env->get("REDIS_READ_WRITE_TIMEOUT", 0);
    resolve(
        'MJohann\Packlib\Facades\SimpleRedis',
        [[$redis_host, $redis_port, $redis_password, $redis_username, $redis_scheme, $redis_read_write_timeout]],
        'init'
    );
} catch (\Throwable $th) {
    log_create($th);
}

try {
    $env = new \Lib\ENV;
    $env->read();
    $rabbitmq_host          = $env->get("RABBITMQ_HOST", "localhost");
    $rabbitmq_port          = $env->get("RABBITMQ_PORT", 5672);
    $rabbitmq_username      = $env->get("RABBITMQ_USERNAME", "user");
    $rabbitmq_password      = $env->get("RABBITMQ_PASSWORD", "password");
    $rabbitmq_persisted     = $env->get("RABBITMQ_PERSISTED", true);
    $rabbitmq_vhost         = $env->get("RABBITMQ_VHOST", "/");
    resolve(
        'MJohann\Packlib\Facades\SimpleRabbitMQ',
        [[$rabbitmq_host, $rabbitmq_port, $rabbitmq_username, $rabbitmq_password, $rabbitmq_persisted, $rabbitmq_vhost]],
        'init'
    );
} catch (\Throwable $th) {
    log_create($th);
}

try {
    $env = new \Lib\ENV;
    $env->read();
    $aes256_secret = $env->get("AES_256_SECRET", "AES_256");
    resolve(
        'MJohann\Packlib\Facades\SimpleAES256',
        [$aes256_secret],
        'init'
    );
} catch (\Throwable $th) {
    log_create($th);
}

try {
    $env = new \Lib\ENV;
    $env->read();
    $authServerUrl          = $env->get("KEYCLOAK_AUTH_SERVER_URL");
    $realm                  = $env->get("KEYCLOAK_REALM");
    $clientId               = $env->get("KEYCLOAK_CLIENT_ID");
    $clientSecret           = $env->get("KEYCLOAK_CLIENT_SECRET");
    $redirectUri            = $env->get("KEYCLOAK_REDIRECT_URI");
    \Lib\SimpleKeycloak::config($authServerUrl, $realm, $clientId, $clientSecret, $redirectUri);
} catch (\Throwable $th) {
    log_create($th);
}

\Lib\Meter::start();

!defined('CLI') ? session() : null;

!defined('CLI') ? \Lib\Route::init() : null;

require_once __DIR__ . "/db_conn_capsule.php";
