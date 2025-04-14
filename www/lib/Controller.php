<?php

namespace Lib;

use MJohann\Packlib\DataManager;

class Controller
{

    private $nameFolderControllers = "";
    private $folderControllers = "";

    public function __construct()
    {
        $this->nameFolderControllers = input_env("NAME_FOLDER_CONTROLLERS");
        $this->folderControllers = realpath(__DIR__ . "/../app/" . $this->nameFolderControllers . "/");
    }

    public function getAllControllers(bool $onlyValid = true)
    {
        $result = [];
        if ($onlyValid) {
            $scan = DataManager::folderScan($this->folderControllers, false, true);
            foreach ($scan as $key => $path) {
                if ($path["type"] == "FILE" && strpos($path["name"], "Controller.php") !== false) {
                    $path["route"] = $this->getRoute($path["path"]);
                    $path["class"] = $this->getClass($path["path"]);
                    if ($path["class"] === null || !property_exists($path["class"], "generateRoutes")) {
                        unset($scan[$key]);
                        continue;
                    }
                    $path["method"] = $this->getAllMethodsAndParameters($this->getClass($path["path"]));
                    $result[] = $path;
                }
                unset($scan[$key]);
            }
        }
        // dumpl($result);
        return $result;
    }

    public function generatePathRoutes(array &$path)
    {
        $path["routes"] = [];
        foreach ($path["method"] as $method => $params) {
            $route_config = [];
            $arguments = [""];
            foreach ($params as $param) {
                // dumpd($param);
                $name = $param["name"];
                if ($name == "CONFIG") {
                    $route_config = $param;
                    continue;
                }
                if ($param["optional"]/* && $param["type"] != "array"*/) {
                    $op = "?";
                    $arguments[] = str_replace("?", "", $arguments[count($arguments) - 1]) . "/{" . $name . ":" . $param["type"] . $op . "}";
                    continue;
                }
                $arguments[count($arguments) - 1] = $arguments[count($arguments) - 1] . "/{" . $name . ":" . $param["type"] . "}";
            }
            // $route_base = strtolower("/" . str_replace("Controller.php", "", $path["name"]) . "/$method");
            $route_base = $path["route"] . "/$method";
            $action = $path["class"] . "@" . $method;
            $arguments = array_reverse($arguments);
            foreach ($arguments as $key => $arg) {
                $_path_route = $route_base . $arg;
                $_method = (string) ($route_config["value"]["method"] ?? "ANY");
                $_csrf = (bool) ($route_config["value"]["csrf"] ?? false);
                $_jwt = (bool) ($route_config["value"]["jwt"] ?? false);
                $_cache = (int) ($route_config["value"]["cache"] ?? -1);
                $_name = $route_config["value"]["name"] ?? null;
                $path["routes"][] = [
                    "path" => $_path_route,
                    "action" => $action,
                    "method" => $_method,
                    "csrf" => $_csrf,
                    "jwt" => $_jwt,
                    "cache" => $_cache,
                    "name" => $_name
                ];
                $index_route = strrpos($_path_route, "/$method");
                if ($method == "index" && $index_route !== false) {
                    $path["routes"][] = [
                        "path" => substr($_path_route, 0, $index_route) . substr($_path_route, $index_route + strlen("/$method")),
                        "action" => $action,
                        "method" => $_method,
                        "csrf" => $_csrf,
                        "jwt" => $_jwt,
                        "cache" => $_cache,
                        "name" => $_name
                    ];
                }
            }
        }
        //dumpd($path["routes"]);
    }

    private function getClass(string $pathController)
    {
        $init = "app/" . $this->nameFolderControllers . "/";
        $indexApp = strpos($pathController, $init);
        if ($indexApp !== false) {
            $pathController = substr($pathController, $indexApp - 1);
            $pathController = substr($pathController, 0, strlen($pathController) - 4);
            $pathController = str_replace("/", "\\", $pathController);
        }
        // dumpl($pathController);
        if (class_exists($pathController)) {
            return $pathController;
        }
        return null;
    }

    private function getRoute(string $pathController)
    {
        $base = "app/" . $this->nameFolderControllers;
        $indexControllers = strpos($pathController, $base);
        if ($indexControllers !== false) {
            $pathController = substr($pathController, $indexControllers + strlen($base));
            $pathController = str_replace("Controller.php", "", $pathController);
            $pathController = strtolower($pathController);
        }
        return $pathController;
    }

    private function getAllMethodsAndParameters(string $class)
    {
        $result = [];
        if (class_exists($class) && property_exists($class, "generateRoutes")) {
            $methods = get_class_methods($class);
            foreach ($methods as $index => $method) {
                $ReflectionMethod = new \ReflectionMethod($class, $method);
                $reflectionParams = $ReflectionMethod->getParameters();
                $result[$method] = [];
                foreach ($reflectionParams as $param) {
                    try {
                        $result[$method][] = [
                            "name" => $param->getName(),
                            "type" => ($param->getType() !== null) ? $param->getType()->getName() : "string", //"type" => (string) $param->getType(),
                            "optional" => $param->isOptional(),
                            "value" => $param->isOptional() ? ($param->isDefaultValueAvailable() ? $param->getDefaultValue() : "") : ""
                        ];
                    } catch (\Throwable $e) {
                        log_create($e);
                    }
                }
                unset($methods[$index]);
            }
        }
        if (count($result) > 0) {
            $keys = array_keys($result);
            $construct = array_search('__construct', $keys);
            if ($construct !== false && $construct >= 0) {
                unset($result["__construct"]);
            }
            $destruct = array_search('__destruct', $keys);
            if ($destruct !== false && $destruct >= 0) {
                unset($result["__destruct"]);
            }
        }
        return $result;
    }
}
