<?php

/*
	GitHub: https://github.com/matheusjohannaraujo/makemvcss
	Country: Brasil
	State: Pernambuco
	Developer: Matheus Johann Araujo
	Date: 2020-12-31
*/

namespace Lib;

use Lib\In;
use Lib\URI;
use Lib\Out;
use Lib\CSRF;
use Lib\Controller;
use Lib\DataManager;

class Route
{

    public static $in;
    public static $out;
    public static $route;

    public static function init()
    {
        self::$in = new In;
        self::$out = new Out;
        self::$route = [];
    }

    public static function in()
    {
        return self::$in;
    }

    public static function out()
    {
        return self::$out;
    }

    private static function stringCountReg($str, $reg = "{/}")
    {
        preg_match_all($reg, $str, $contador);
        return count($contador[0]);
    }

    private static function stringRemoveFinalStripe($str)
    {
        $len = strlen($str);
        if ($len > 0) {
            if (substr($str, $len - 1, $len) == "/") {
                $str = substr($str, 0, $len - 1);
            }
        }
        return $str;
    }

    private static function uri()
    {
        $uri = self::$in->paramServer("REQUEST_URI");        
        $uriSize = (int) strlen($uri);
        $base = URI::scriptName();
        if ($base == "//") {
            $base = "/";
        }
        $baseSize = strlen($base);       
        $baseUriIndex = strpos($uri, $base);
        if ($baseUriIndex !== false && $baseUriIndex >= 0 && $uriSize > 0 && $baseSize >= 0) {
            $uri = substr($uri, $baseSize);
        }
        $uri = self::uriParams($uri);
        // dumpd($uri, $base, $baseUriIndex);
        return $uri;
    }

    private static function uriParams($uri)
    {
        $index = strpos($uri, "?");        
        if ($index !== false && $index >= 0) {
            $params = "";
            $params = substr($uri, $index + 1);
            $uri = substr($uri, 0, $index);
            $uriSignalInterrogation = strripos($params, "?");
            $uriSignalEqual = strpos($params, "=");
            if (!!$uriSignalInterrogation && $uriSignalInterrogation >= 0 && $uriSignalEqual >= 1) {
                $params2 = explode("&", $params);
                $params2[0] = $params;
                $params3 = substr($params, $uriSignalInterrogation + 1);
                $params3 = explode("&", $params3);
                foreach ($params3 as $value) {
                    if (($x = array_search($value, $params2) ?? false)) {
                        $params2[$x] = $value;
                    } else {
                        $params2[] = $value;
                    }
                }
                $params = $params2;
                $_GET = [];
                foreach ($params as $value) {
                    $index = strpos($value, "=");
                    if ($index) {
                        $key = substr($value, 0, $index);
                        $val = substr($value, $index + 1);
                        $_GET[$key] = $val;
                        $_REQUEST[$key] = $val;
                    }
                }
                self::$in->setGet($_GET);
                self::$in->setReq($_REQUEST);
            }
        }
        return self::stringRemoveFinalStripe($uri);
    }

    private static function path($path)
    {        
        return self::stringRemoveFinalStripe(substr($path, 1, strlen($path)));
    }

    private static function uriArray($uri)
    {
        return explode("/", $uri);
    }

    private static function pathArray($path)
    {
        $pathParts = explode("/", $path);
        $array = [];
        for ($i = 0, $j = count($pathParts); $i < $j; $i++) {
            // dumpd($pathParts);
            $name = $pathParts[$i];
            $var = false;
            $type = "string";
            $req = true;            
            if (self::stringCountReg($name, "({.+?}/?)")) {
                $name = str_replace(['{', '}'], '', $name);
                $var = true;
                if (((int) strpos($name, "?")) > 0) {
                    $name = str_replace('?', '', $name);
                    $req = false;
                }
                $index = strpos($name, ":");
                if ($index !== false) {
                    $type = substr($name, $index + 1);
                    $name = substr($name, 0, $index);                    
                }
            }
            if ($name != "") {
                $array[] = [
                    "name" => $name,
                    "var" => $var,
                    "type" => $type,
                    "req" => $req
                ];
            }
        }
        return $array;
    }

    private static function matchPath(string $path)
    {
        $search = false;
        foreach (self::$route as $key => $route) {
            if ($route["name"] == $path || $route["path"] == $path) {
                $search = $route["path"];
                break;
            }
        }
        return $search;
    }

    public static function link(string $path = "", array $params = [])
    {
        $route_name_exist = self::matchPath($path);
        if (!$route_name_exist) {
            self::createAllRoutesStartingFromControllerAndMethod();
            $route_name_exist = self::matchPath($path);
        }
        if ($route_name_exist) {
            $path = $route_name_exist;
        }
        $link = URI::base(true);
        $inc = self::stringCountReg($path, "({.+?}/?)");
        if ($inc <= 0) {
            if ($path[0] != "/") {
                $path = "/" . $path;
            }
            $link .= $path;
        } else if ($inc > 0) {
            $pathParts = self::pathArray($path);
            $i = 0;
            foreach ($pathParts as $key => $pathPart) {
                // dumpd($pathParts);
                if ($pathPart["var"] === false) {
                    $link .= "/" . $pathPart["name"];
                } else if ($pathPart["var"] === true && $pathPart["req"] === true) {
                    $link .= "/" . ($params[$i++] ?? die("Param `" . $pathPart["name"] . "` not found"));
                }
            }
        }
        // dumpd($path, $link);
        return $link;
    }

    //private static function route(string $path, $action, bool $csrf = false, bool $jwt = false, int $cache = -1)
    private static function route(array &$route)
    {
        $path = $route["path"];
        $path = self::path($path);
        $uri = self::uri();
        $route["uri"] = &$uri;
        $isRoute = false;
        $arg = [];
        $inc = self::stringCountReg($path, "({.+?}/?)");
        if (self::stringCountReg($path) == self::stringCountReg($uri) && strtolower($uri) == strtolower($path) && $inc <= 0) {
            $isRoute = true;
        } else if ($inc > 0) {
            $uriParts = self::uriArray(urldecode($uri));
            $pathParts = self::pathArray($path);
            for ($i = 0, $j = count($pathParts); $i < $j; $i++) {
                $isRoute = false;
                $pathPart = $pathParts[$i];
                $uriPart = $uriParts[$i] ?? "";
                unset($uriParts[$i]);
                if (!$pathPart["var"] && strtolower($pathPart["name"]) == strtolower($uriPart)) {
                    $isRoute = true;
                } else if ($pathPart["var"]) {
                    if ($pathPart["req"] && $uriPart != "") {
                        // dumpd($pathPart, $uriPart, is_type($pathPart["type"], $uriPart));
                        if ($pathPart["type"] == "array") {
                            $arg[$pathPart["name"]] = array_map(function($val){
                                return string_to_type($val);
                            }, array_merge([$uriPart], array_values($uriParts)));
                            $isRoute = true;
                            $pathParts = [];
                            $uriParts = [];
                            break;
                        } else if(is_type($pathPart["type"], $uriPart)) {
                            $arg[$pathPart["name"]] = string_to_type($uriPart);
                            $isRoute = true;
                        }                        
                    } else if (!$pathPart["req"]) {
                        $isRoute = true;
                        if ($uriPart != "") {
                            if ($pathPart["type"] == "array") {
                                $arg[$pathPart["name"]] = array_map(function($val){
                                    return string_to_type($val);
                                }, array_merge([$uriPart], array_values($uriParts)));                            
                                $pathParts = [];
                                $uriParts = [];
                                break;
                            } else if(is_type($pathPart["type"], $uriPart)) {
                                $arg[$pathPart["name"]] = string_to_type($uriPart);
                            }
                        }
                    }
                } else {
                    break;
                }
            }
            if ($isRoute && count($uriParts) > 0) {                
                $arg["additional"] = array_values($uriParts);
            }            
        }
        $route["arg"] = &$arg;
        $route["isRoute"] = &$isRoute;
        // dumpd($route, $_REQUEST, $_SERVER);
        return self::verifyIsRoute($route);
    }

    private static function classControllerToControllerName(string $class)
    {
        $folderControllerName = input_env("NAME_FOLDER_CONTROLLERS");
        $index = strpos($class, "${folderControllerName}\\");
        if ($index !== false) {
            $class = substr($class, $index + strlen("${folderControllerName}\\"));                                
        }
        $class = strtolower($class);
        $class = str_replace(["controller", "@", "\\"], ["", ".", "."], $class);
        return $class;
    }

    private static function verifyIsRoute(array &$route)
    {
        try {
            $path = &$route["path"];
            $action = &$route["action"];
            $isRoute = $route["isRoute"];
            $arg = $route["arg"];
            $csrf = &$route["csrf"];
            $jwt = &$route["jwt"];            
            $cache = &$route["cache"];
            unset($route["arg"]);
            unset($route["uri"]);
            unset($route["isRoute"]);
            if ($isRoute) {
                if ($csrf && !CSRF::valid()) {
                    self::$out->pageCSRF();
                }
                if ($jwt && !self::$in->paramJwt()->valid()) {
                    self::$out->pageJWT();
                }
                $result = "";
                self::$in->setArg($arg);
                self::$out->cache("R:${path}", $cache);                
                if (is_callable($action)) {
                    $result = $action(...array_values(self::$in->paramArg()));
                } else {
                    $action = (string) $action;
                    $ControllerMethod = explode("@", $action);
                    if (count($ControllerMethod) == 2) {
                        $Controller = $ControllerMethod[0];
                        if (!class_exists($Controller)) {
                            $folderControllerName = input_env("NAME_FOLDER_CONTROLLERS");
                            $Controller = "\app\\${folderControllerName}\\" . $Controller;
                        }
                        $Method = $ControllerMethod[1];
                        if (class_exists($Controller) && method_exists($Controller, $Method)) {
                            $filename = self::classControllerToControllerName($Controller) . "_" . $Method;
                            self::$out->filename($filename);
                            $result = (function (&$Controller, &$Method) {
                                try {
                                    return (new $Controller)->$Method(...array_values(self::$in->paramArg()));
                                } catch (\Throwable $e) {
                                    dumpd($e->getMessage());
                                }
                            })($Controller, $Method);
                        } else {
                            self::$out->page404();
                        }
                    } else {
                        $result = view($action, self::$in);
                    }
                }                
                if ($result instanceof Route) {
                    $result->out->go();
                } else if ($result instanceof Out) {
                    $result->go();
                } else {
                    self::$out
                        ->content($result)
                        ->go();
                }
                return true;
            }
        } catch (\Throwable $e) {
            dumpd($e);
        }
        return false;
    }

    private static function type($action)
    {
        if (!is_string($action) && is_callable($action)) {
            return "closure";
        } else if (is_string($action)) {
            $x = strpos($action, "@");
            if ($x  && $x > 0) {
                return "controller";
            } else {
                return "view";
            }                    
        }
        return "undefined";
    }

    public static function method(string $method)
    {
        $index = count(self::$route) - 1;
        if ($index >= 0) {
            self::$route[$index]["method"] = strtoupper($method);
        }
        return __CLASS__;
    }

    public static function name(string $name)
    {
        $index = count(self::$route) - 1;
        if ($index >= 0) {
            self::$route[$index]["name"] = $name;
        }
        return __CLASS__;
    }

    public static function csrf(bool $csrf)
    {
        $index = count(self::$route) - 1;
        if ($index >= 0) {
            self::$route[$index]["csrf"] = (int) $csrf;
        }
        return __CLASS__;
    }

    public static function jwt(bool $jwt)
    {
        $index = count(self::$route) - 1;
        if ($index >= 0) {
            self::$route[$index]["jwt"] = (int) $jwt;
        }
        return __CLASS__;
    }

    public static function cache(int $cache)
    {
        $index = count(self::$route) - 1;
        if ($index >= 0) {
            self::$route[$index]["cache"] = $cache;
        }
        return __CLASS__;
    }

    public static function parseName(string $name){
        $name = strtolower($name);
        if (!empty($name) && strlen($name) > 2) {
            $name = substr($name, 1);
            $firstBar = strpos($name, "/{");
            if ($firstBar !== false) {
                $name = substr($name, 0, $firstBar);
            }
        }
        $name = str_replace("/", ".", $name);
        $name = str_replace(["{", "}", "?"], "", $name);
        return $name;
    }

    public static function action($action)
    {
        $index = count(self::$route) - 1;
        if ($index >= 0) {
            $type = self::type($action);
            self::$route[$index]["action"] = $action;
            self::$route[$index]["type"] = $type;
        }
        return __CLASS__;
    }

    private static function defRoute(string $method, string $path, $action, string $name = null, bool $csrf = false, bool $jwt = false, int $cache = -1)
    {
        self::$route[] = [
            "method" => strtoupper($method),
            "path" => $path,
            "action" => $action,
            "type" => self::type($action),
            "name" => $name ?? ((self::type($action) == "controller") ? self::classControllerToControllerName($action) : self::parseName($path)),
            "csrf" => (int) $csrf,
            "jwt" => (int) $jwt,
            "cache" => (int) $cache,
        ];
        return __CLASS__;
    }

    public static function any(string $path, $action, string $name = null, bool $csrf = false, bool $jwt = false, int $cache = -1)
    {
        return self::defRoute("ANY", $path, $action, $name, $csrf, $jwt, $cache);
    }

    public static function get(string $path, $action, string $name = null, bool $csrf = false, bool $jwt = false, int $cache = -1)
    {
        return self::defRoute("GET", $path, $action, $name, $csrf, $jwt, $cache);
    }

    public static function post(string $path, $action, string $name = null, bool $csrf = false, bool $jwt = false, int $cache = -1)
    {
        return self::defRoute("POST", $path, $action, $name, $csrf, $jwt, $cache);
    }

    public static function put(string $path, $action, string $name = null, bool $csrf = false, bool $jwt = false, int $cache = -1)
    {
        return self::defRoute("PUT", $path, $action, $name, $csrf, $jwt, $cache);
    }

    public static function patch(string $path, $action, string $name = null, bool $csrf = false, bool $jwt = false, int $cache = -1)
    {
        return self::defRoute("PATCH", $path, $action, $name, $csrf, $jwt, $cache);
    }

    public static function options(string $path, $action, string $name = null, bool $csrf = false, bool $jwt = false, int $cache = -1)
    {
        return self::defRoute("OPTIONS", $path, $action, $name, $csrf, $jwt, $cache);
    }

    public static function delete(string $path, $action, string $name = null, bool $csrf = false, bool $jwt = false, int $cache = -1)
    {
        return self::defRoute("DELETE", $path, $action, $name, $csrf, $jwt, $cache);
    }

    public static function match(array $verbs, string $path, $action, string $name = null, bool $csrf = false, bool $jwt = false, int $cache = -1)
    {
        $result = [];
        foreach ($verbs as $verb) {
            $result[] = self::defRoute($verb, $path, $action, $name, $csrf, $jwt, $cache);
        }
        return $result;
    }

    private static function runRoute()
    {
        $result = false;
        $METHOD = strtoupper(self::$in->paramServer("REQUEST_METHOD"));
        $METHOD = strtoupper(self::$in->paramReq("_method", $METHOD));
        foreach (self::$route as $key => &$route) {
            if ($METHOD === $route["method"] || "ANY" === $route["method"]) {
                $result = self::route($route);
            }
            if ($result) {
                break;
            }
        }
        return $result;
    }

    private static function routesDump(string $method, int $index = -1)
    {
        // dumpd($method, $id);
        $method = strtoupper($method);
        $result = [];
        foreach (self::$route as $key => $route) {
            if ($method === "ANY" || $method === $route["method"] || "ANY" === $route["method"]) {
                if ($route["type"] == "closure") {
                    $route["action"] = "function";
                }
                $result[] = $route;
            }
        }
        if ($index != -1) {
            return $result[$index];
        }
        return $result;
    }

    public static function on()
    {
        self::post("/thread_http", function() {
            // GitHub: https://github.com/matheusjohannaraujo/php_thread_parallel
            $script = input_post("script", "");
            if (!empty($script)) {
                ob_start();
                try {
                    $aes = new AES_256;
                    $script = base64_decode($script);
                    $script = $aes->decrypt_cbc($script);
                    eval($script);
                } catch (\Throwable $th) {
                    var_export($th);
                }
                $script = ob_get_clean();
            } else {
                $script = "";
            }
            die(base64_encode($script));
        })::jwt(true);
        if (!self::runRoute()) {
            $uri = self::uri();
            $path = DataManager::path(realpath(__DIR__ . "/../public") . "/$uri");
            // dumpd($uri, $path);
            if (DataManager::exist($path) && $uri != "" && $uri != "/") {
                $uri = URI::base() . "public/" . $uri;
                redirect()->to($uri);
            }
            if (input_env("ENV") === "development") {
                self::get("/routes/all/json/{method:string?}", function (string $method = "ANY") {
                    return self::routesDump($method);
                });
                self::get("/routes/all/{method:string?}", function (string $method = "ANY") {
                    dumpd(self::routesDump($method));
                });
                self::get("/routes/{index:int}/{method:string?}", function (int $index, string $method = "ANY") {
                    dumpd(self::routesDump($method, $index));
                });
            }
            self::createAllRoutesStartingFromControllerAndMethod();
            if (input_env("AUTO_VIEW_ROUTE")) {
                $avrs = self::getAllAutoViewRoutes();
                self::generateAutoViewRoutes($avrs);
                // dumpd($avrs, self::$route);
            }
            if (($action = input_env("INIT_ACTION_APP")) !== null) {
                self::get("/", $action);
            }            
            // dumpd(self::$route);
            if (!self::runRoute()) {
                // If no route is served, it returns an html page containing the 404 error.
                self::$out->page404();
            }
        }
    }

    private static function getAllAutoViewRoutes()
    {
        $folderViewName = input_env("NAME_FOLDER_VIEWS");
        $folderView = DataManager::folderScan(realpath(__DIR__ . "/../app/${folderViewName}/"), false, true);
        $result = [];
        foreach ($folderView as $key => $path) {
            if ($path["type"] == "FILE" && strpos($path["name"], "avr-") !== false) {
                $indexAppView = strpos($path["path"], "/app/${folderViewName}/");
                if ($indexAppView !== false) {
                    $path["route_path"] = substr($path["path"], $indexAppView + strlen("/app/${folderViewName}/") - 1);
                } else {
                    $path["route_path"] = "/" . $path["name"];                
                }
                $name = $path["route_path"];
                $path["route_path"] = str_replace(["avr-", ".php"], "", $name);
                $path["route_name"] = str_replace(".php", "", $name);
                $result[] = $path;
            }
        }
        return $result;
    }

    private static function generateAutoViewRoutes(array &$avrs)
    {
        foreach ($avrs as $key => $avr) {
            self::any($avr["route_path"], $avr["route_name"]);
        }
    }

    private static function getAllControllersAndMethods($only_valid = true)
    {
        $controller = new Controller;
        $allControllers = $controller->getAllControllers($only_valid);
        foreach ($allControllers as &$path) {            
            $controller->generatePathRoutes($path);            
        }
        // dumpd($allControllers);
        return $allControllers;
    }

    private static function createRoute(array &$path)
    {
        foreach ($path["routes"] as $key => $route) {
            $path["routes"][$key] = self::any($route["path"], $route["action"])::method($route["method"])::csrf($route["csrf"])::jwt($route["jwt"])::cache($route["cache"]);
            if (!empty($route["name"]) && is_string($route["name"])) {
                $path["routes"][$key]::name($route["name"]);
            }
            unset($path["routes"][$key]);
        }
        unset($path);
    }

    private static function createAllRoutesStartingFromControllerAndMethod()
    {
        $controllers = self::getAllControllersAndMethods(input_env("GENERATE_SIGNED_CONTROLLER_ROUTES_ONLY", true));
        $route_backup = self::$route;
        self::$route = [];
        for ($i = 0, $j = count($controllers); $i < $j; $i++) { 
            self::createRoute($controllers[$i]);
        }
        // dumpd(self::$route);
        self::$route = array_merge(self::$route, $route_backup);
    }

}
