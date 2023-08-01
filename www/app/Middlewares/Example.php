<?php

namespace App\Middlewares;

class Example {

    public static function handle($route, \Closure $next) :bool
    {
        $bool = (bool) rand(0, 1);
        if (!$bool) {
            dumpl("App\Middlewares\Example::handle", $route);
        }
        return $next($bool);
    }

}
