<?php

/*
	GitHub: https://github.com/matheusjohannaraujo/makemvcss
	Country: Brasil
	State: Pernambuco
	Developer: Matheus Johann Araujo
	Date: 2020-06-23
*/

namespace Lib;

use Lib\DataManager;

class Meter
{

    private static $time = 0;
    private static $memory_g_u = 0;
    private static $memory_g_p_u = 0;

    public static function getTime()
    {
        $microtime = explode(" ", microtime());
        $time = $microtime[0] + $microtime[1];
        return $time;
    }

    public static function start()
    {
        self::$time = self::getTime();
        self::$memory_g_u = memory_get_usage(true);
        self::$memory_g_p_u = memory_get_peak_usage(true);
    }

    public static function stop($kill = false)
    {
        $timeStop = self::getTime();
        $finalTime = $timeStop - self::$time;
        $finalMemory_g_u = memory_get_usage(true) - self::$memory_g_u;
        $finalMemory_g_p_u = memory_get_peak_usage(true) - self::$memory_g_p_u;
        $result = [
            "time" => [
                "diff" => number_format($finalTime, 6) . " ms",
                "start" => number_format(self::$time, 6) . " ms",
                "stop" => number_format($timeStop, 6) . " ms",
            ],
            "memory" => [
                "media" => DataManager::size(($finalMemory_g_p_u + $finalMemory_g_u) / 4),
                "mgu" => DataManager::size($finalMemory_g_u / 2),
                "mgpu" => DataManager::size($finalMemory_g_p_u / 2),
            ],
        ];
        if ($kill) {
            dumpd($result);
        }
        return $result;
    }

}
