<?php

/*
	GitHub: https://github.com/matheusjohannaraujo/makemvcss
	Country: Brasil
	State: Pernambuco
	Developer: Matheus Johann Araujo
	Date: 2020-10-13
*/

namespace Lib;

use Lib\Route;

class CSRF
{

    public static function create()
    {
        session()->set("_csrf", hash("sha256", uniqid() . "_MakeMVCSS_" . uniqid()));
    }
    
    public static function get()
    {
        if (!session()->has("_csrf")) {
            self::create();
        }
        return session()->get("_csrf");
    }
    
    public static function valid($csrf = false)
    {
        $in = Route::$in;
        $valid = false;
        if ($csrf) {
            $valid = self::get() == $csrf;
        } else {            
            $valid = self::get() == $in->paramReq("_csrf", $in->paramJson("_csrf"));
        }
        if ($valid && $in->paramEnv("CSRF_REGENERATE", false)) {
            self::create();
        }
        return $valid;
    }

}
