<?php

/*
	GitHub: https://github.com/matheusjohannaraujo/makemvcss
	Country: Brasil
	State: Pernambuco
	Developer: Matheus Johann Araujo
	Date: 2021-02-23
*/

namespace Lib;

use Lib\DataManager;

class ENV
{

    private $raw = "";
    private $env = [];
    private $path_env = null;

    public function __construct()
    {
        $this->path_env = __DIR__ . "/../.env";
    }

    public function get(string $key = null, $default_value = null)
    {
        if ($key !== null) {
            return $this->env[$key] ?? $default_value;
        }
        return $this->env ?? $default_value;        
    }

    public function raw()
    {
        return $this->raw;
    }

    public function read(string $path_env = null)
    {
        if ($path_env === null) {
            $path_env = $this->path_env;
            if (!DataManager::exist($path_env)) {
                $path_env_example = __DIR__ . "/../.env.example";
                if (!DataManager::exist($path_env_example)) {
                    dumpd("The `.env` and `.env.example` files were not found.");
                }
                DataManager::copy($path_env_example, ".env") or dumpd("It was not possible to copy the `.env.example` file to create the `.env`.");
            }
        } else {
            $this->path_env = $path_env;
        }
        $env = [];        
        $this->raw = "";
        $gen = DataManager::fileRead($path_env, 3);
        foreach ($gen as $key => $value) {
            $this->raw .= $value;
            $value = trim($value);
            $indexEqual = strpos($value, "=");
            if ($value != "" && $value[0] != "#" && $indexEqual !== false) {
                $key = trim(substr($value, 0, $indexEqual));
                $value = trim(substr($value, $indexEqual + 1));
                if (strlen($key) > 0) {
                    $env[$key] = string_to_type($value);
                }
                // dumpd($key, $value, $env);
            }
            unset($key);
            unset($value);
        }
        return $this->env = &$env;
    }

    public function write(array $array = [], string $path_env = null)
    {
        if ($path_env === null) {
            $path_env = $this->path_env;
        } else {
            $this->path_env = $path_env;
        }
        $str = "";
        foreach ($array as $key => $value) {
            $str .= trim($key) . "=" . trim($value) . "\r\n";
            unset($array[$key]);
        }
        unset($array);
        return DataManager::fileWrite($path_env, $str) && !empty($this->read());
    }

    public function required()
    {
        $env_keys_required = [
            "ENV",
            "CSRF_REGENERATE",
            "JWT_SECRET",
            // "DB_CONNECTION",
            // "DB_HOST",
            // "DB_PORT",
            // "DB_CHARSET",
            // "DB_CHARSET_COLLATE",
            // "DB_USERNAME",
            // "DB_PASSWORD",
            // "DB_DATABASE"
        ];
        $env_keys = array_keys($this->env);
        foreach ($env_keys_required as $key) {        
            if (!in_array($key, $env_keys)) {
                dumpd("The definition of `$key` was not found in the `.env` file");
            }
        }
    }

    public function merge()
    {
        $this->env = array_merge($this->env, $_ENV);
        return $_ENV = &$this->env;
    }

}
