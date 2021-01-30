<?php

/*
	GitHub: https://github.com/matheusjohannaraujo/makemvcss
	Country: Brasil
	State: Pernambuco
	Developer: Matheus Johann Araujo
	Date: 2021-01-30
*/

// https://github.com/illuminate/database
// https://medium.com/@kshitij206/use-eloquent-without-laravel-7e1c73d79977
// https://laravel.com/docs/5.0/schema
// https://www.amitmerchant.com/how-to-utilize-capsule-use-eloquent-orm-outside-laravel/
// composer require "illuminate/database"
// composer require "illuminate/events"
// composer require "illuminate/support"

use Lib\ENV;
use Lib\DataManager;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\DB;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;

$env = new ENV;
$env->read();

if ($env !== null && $env->get("DB_CONNECTION") && class_exists("Illuminate\Database\Capsule\Manager")) {

    $capsule = new Capsule;
    if ($env->get("DB_CONNECTION") == "sqlite") {
        $database = $env->get("DB_DATABASE", __DIR__ . '/../database') . ".sqlite";
        if (DataManager::exist($database) === null) {
            DataManager::fileCreate($database);
        }
        $capsule->addConnection([
            'driver'   => $env->get("DB_CONNECTION"),
            'database' => $database,
            'prefix'   => $env->get("DB_PREFIX", "")
        ]); 
    } else {
        $capsule->addConnection([
            'driver'    => $env->get("DB_CONNECTION"),
            'host'      => $env->get("DB_HOST"),
            'port'      => $env->get("DB_PORT"),
            'database'  => $env->get("DB_DATABASE"),
            'username'  => $env->get("DB_USERNAME"),
            'password'  => $env->get("DB_PASSWORD"),
            'charset'   => $env->get("DB_CHARSET"),
            'collation' => $env->get("DB_CHARSET_COLLATE"),
            'prefix'    => $env->get("DB_PREFIX", "")
        ]);
    }
    
    // Set the event dispatcher used by Eloquent models... (optional)
    $capsule->setEventDispatcher(new Dispatcher(new Container));
    
    // Make this Capsule instance available globally via static methods... (optional)
    $capsule->setAsGlobal();
    
    // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
    $capsule->bootEloquent();

    DB::setFacadeApplication(new Container());
    DB::swap($capsule->getDatabaseManager());
    
    date_default_timezone_set($env->get("TIMEZONE"));

    /*function DB() {
        global $capsule;
        return $capsule;
    }*/
    
    function path_schema_apply(string $path) {
        $env = new ENV;
        $env->read();
        if ($env === null) {
            die("\$env is null");
        }
        $folderSchemaName = $env->get("NAME_FOLDER_SCHEMAS");
        if (DataManager::exist($path) == "FILE") {
            $path = realpath($path);
            require_once $path;
            echo "\r\nSchema Apply Success: app/${folderSchemaName}/" . pathinfo($path)['basename'] . "\r\n";
        } else {
            echo "\r\nSchema Apply Error: app/${folderSchemaName}/" . pathinfo($path)['basename'] . "\r\n";
        }
    }
    
    function db_schemas_apply(string $nameFile)
    {     
        $env = new ENV;
        $env->read();
        if ($env === null) {
            die("\$env is null");
        }      
        $folderSchemaName = $env->get("NAME_FOLDER_SCHEMAS");
        $nameFile = strtolower($nameFile);
        $base = __DIR__ . "/../app/$folderSchemaName/";
        if ($nameFile == "-a" || $nameFile == "--all") {
            $schemas = DataManager::folderScan(realpath($base));
            foreach ($schemas as $key => $schema) {
                $index = strpos($schema["name"], "s_capsule.php");
                if ($schema["type"] == "FILE" && $index !== false && $index > 0) {
                    path_schema_apply($schema["path"]);
                }                
            }
        } else {
            $path = "${base}${nameFile}s_capsule.php";
            path_schema_apply($path);
        }
    }

}
