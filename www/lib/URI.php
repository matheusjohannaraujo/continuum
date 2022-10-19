<?php

namespace Lib;

class URI
{

    private static $protocol;
    private static $host;
    private static $scriptName;
    private static $finalBase;

    public static function Protocol()
    {
        self::$protocol = null;        
        $protocol = strtolower($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? "");
        $protocol = explode(",", $protocol);
        if (count($protocol) > 0) {
            $protocol = $protocol[0];
        } else {
            $protocol = strtolower($_SERVER['REQUEST_SCHEME'] ?? "");
        }
        switch ($protocol) {
            case "http":
                self::$protocol = 'http://';
                break;
            case "https":
                self::$protocol = 'https://';
                break;
        }
        if (self::$protocol === null) {
            if (strpos(strtolower($_SERVER['SERVER_PROTOCOL'] ?? ''), 'https') === false) {
                self::$protocol = 'http://';
            } else {
                self::$protocol = 'https://';
            }
        }
        return self::$protocol;
    }

    public static function Host()
    {
        self::$host = $_SERVER['HTTP_HOST'] ?? '';
        return self::$host;
    }

    public static function scriptName()
    {
        $scr = dirname($_SERVER['SCRIPT_NAME']);
        if (!empty($scr) || substr_count($scr, '/') > 1) {
            self::$scriptName = $scr . '/';
        } else {
            self::$scriptName = '';
        }
        self::$scriptName = str_replace("\\", "", self::$scriptName);
        self::$scriptName = str_replace("//", "/", self::$scriptName);
        return self::$scriptName;
    }

    public static function base($bar = false)
    {
        self::$finalBase = input_env("APP_URL", self::Protocol() . self::Host() . self::scriptName());
        if ($bar) {
            return substr(self::$finalBase, 0, strlen(self::$finalBase) -1);
        }
        return self::$finalBase;
    }

    public static function site($file = "")
    {
        return self::base() . "$file";
        // return self::base() . $file;
    }

    public static function public($file = "")
    {
        return self::base() . "public/$file";
        // return self::base() . $file;
    }
    
    public static function css($file)
    {
        $ext = ((pathinfo($file)["extension"] ?? null) == "css") ? "" : ".css";
        return self::public("css/$file$ext");
    }
    
    public static function js($file)
    {
        $ext = ((pathinfo($file)["extension"] ?? null) == "js") ? "" : ".js";
        return self::public("js/$file$ext");
    }
    
    public static function img($file)
    {
        return self::public("img/$file");
    }

}
