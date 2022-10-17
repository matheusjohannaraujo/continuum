<?php

namespace App\Middlewares;

class Example {

    public static function handle($route, \Closure $next) :bool
    {
        dumpl("App\Middlewares\Example::handle", $route);
        return $next(rand(0, 1));
    }

}
