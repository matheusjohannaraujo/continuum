<?php

/*
	GitHub: https://github.com/matheusjohannaraujo/makemvcss
	Country: Brasil
	State: Pernambuco
	Developer: Matheus Johann Araujo
	Date: 2020-11-05
*/

namespace Lib;

use Lib\ENV;
use Lib\DataManager;

class Cache
{

    private $folderCache = "";
    private $name = null;
    private $path = null;
    private $pathInfo = null;
    private $pathContent = null;

    public function __construct()
    {
        $this->folderCache = folder_storage("cache/");
    }

    private function get_path(string $name)
    {
        return $this->folderCache . md5($name) . "/";
    }

    public function init(string $name, int $time)
    {
        $this->name = $name;
        $path = $this->get_path($this->name);
        if ($time >= 0) {
            if (!DataManager::exist($path)) {
                DataManager::folderCreate($path);
            }
            $valid_until = "";
            if ($time === 0) {
                $valid_until = "infinite";
            } else {
                $valid_until = date("Y-m-d H:i:s", time() + $time);
            }
            $info = [
                "VALID_UNTIL" => $valid_until,
                "TIME_REGISTRED" => $time,
                "CREATED_AT" => date("Y-m-d H:i:s"),
                "NAME" => $name
            ];
            if (DataManager::exist($path) == "FOLDER") {
                $this->path = $path;
                $this->pathInfo = $path . "info.txt";
                $this->pathContent = $path . "content.txt";
                return !empty($this->info($info)) && DataManager::fileWrite($this->pathContent, "");
            }
        } else if ($time === -1 && DataManager::exist($path) == "FOLDER") {
            DataManager::delete($path);
        }
        return false;
    }

    public function env()
    {
        $env = new ENV;
        if ($this->pathInfo !== null) {
            $env->read($this->pathInfo);
        }
        return $env;
    }

    public function info($info = null)
    {
        $env = $this->env();
        if ($this->pathInfo !== null && is_array($info)) {
            $env->write($info, $this->pathInfo);
        }
        return $env->get();
    }

    public function delete()
    {        
        if (DataManager::exist($this->path) == "FOLDER") {
            return DataManager::delete($this->path);
        }
        return false;
    }

    public function put(string $content)
    {
        $result = false;
        if ($this->pathContent !== null) {
            $result = DataManager::fileWrite($this->pathContent, $content);
        }
        unset($content);
        return $result;        
    }

    public function append(string $content)
    {
        $result = false;
        if ($this->pathContent !== null) {
            $result = DataManager::fileAppend($this->pathContent, $content);
        }
        unset($content);
        return $result;
    }

    public function get()
    {
        if ($this->pathContent !== null) {
            return DataManager::fileRead($this->pathContent);
        }
        return null;
    }

    public function get_paths(string $name)
    {        
        $path = $this->get_path($name);
        $pathInfo = $path . "info.txt";
        $pathContent = $path . "content.txt";
        $location = [];
        if (
            DataManager::exist($path) == "FOLDER" &&
            DataManager::exist($pathInfo) == "FILE" &&
            DataManager::exist($pathContent) == "FILE"
        ) {
            $this->name = $name;
            $this->path = $path;
            $this->pathInfo = $pathInfo;
            $this->pathContent = $pathContent;
            $location = [
                "path" => $path,
                "info" => $pathInfo,
                "content" => $pathContent
            ];
        }
        return $location;
    }

    public function is_valid_paths(array $location, int $time)
    {
        if (!empty($location) && count($location) == 3) {
            $env = $this->env();
            $timeLast = $env->get("VALID_UNTIL");
            $timeRegistred = $env->get("TIME_REGISTRED");
            if ($timeRegistred != $time) {
                $time = -1;
            }
            if ($time === -1 && DataManager::exist($location["path"]) == "FOLDER") {
                DataManager::delete($location["path"]);
                return false;
            }            
            $timeNow = new \DateTime('now', new \DateTimeZone('America/Sao_Paulo'));
            if ($timeLast === "infinite") {
                $timeLast = new \DateTime(date("Y-m-d H:i:s", time() + 60));
            } else {
                $timeLast = new \DateTime($timeLast);
            }                    
            if ($timeNow->diff($timeLast)->invert === 0) {
                return true;
            } else {
                DataManager::delete($location["path"]);
            }            
        }
        return false;
    }

    public function exist(string $name, int $time)
    {
        $location = $this->get_paths($name);
        return $this->is_valid_paths($location, $time);
    }

}
