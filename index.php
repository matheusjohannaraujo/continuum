<?php

declare(ticks=1);
declare(strict_types=1);
$vendor = __DIR__ . "/vendor/";
$autoload = $vendor . "autoload.php";

function config_init()
{
	ini_set("set_time_limit", "3600");
	ini_set("max_execution_time", "3600");
	ini_set("default_socket_timeout", "3600");
	ini_set("memory_limit", "6144M");
}

config_init();

/*
if ((($_GET["adm"] ?? null) == "phpinfo")) {
	phpinfo();
	die;
}

if ((($_GET["adm"] ?? null) == "unzip") && file_exists($vendor) && is_dir($vendor)) {
	require_once __DIR__ . "/lib/DataManager.php";
	\Lib\DataManager::zipUnzipFolder("./vendor/", "unzip");
	die("End unzip");
}

if ((($_GET["adm"] ?? null) == "config") && file_exists($autoload)) {
	require_once $autoload;
	require_once __DIR__ . "/lib/db_conn_capsule.php";
	db_schemas_apply("config");
	die;
}
*/

if (!file_exists($autoload)) {
	shell_exec("composer update --ignore-platform-reqs");
}

if (file_exists($autoload)) {
	require_once $autoload;
	require_once __DIR__ . "/lib/config.php";
	require_once __DIR__ . "/app/web.php";
} else {
	require_once __DIR__ . "/lib/DataManager.php";
	die("<br><center># The `" . \Lib\DataManager::path($autoload) . "` not found. If you are reading this message, open a command prompt inside the project folder and run the command below:<hr><h1><b>composer update</b></h1></center>");
}
