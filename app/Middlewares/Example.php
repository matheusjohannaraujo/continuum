<?php

namespace App\Middlewares;

class Example {

    public static function handle($route, \Closure $next) :bool
    {
        dumpl("handle", $route);
        return $next(rand(0, 1));
    }

}
