<?php

namespace Lib;

use MJohann\Packlib\DataManager;

class Meter
{

    private static $timestampStart = null;
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
        self::$timestampStart = (new \DateTime())->format("Y-m-d H:i:s.u");
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
        $timestampStart = self::$timestampStart;
        $timestampStop = (new \DateTime())->format("Y-m-d H:i:s.u");
        $start = \DateTime::createFromFormat('Y-m-d H:i:s.u', $timestampStart);
        $stop = \DateTime::createFromFormat('Y-m-d H:i:s.u', $timestampStop);
        $timestampDiff = ($start->diff($stop))->format('%y years, %m months, %d days, %h hours, %i minutes, %s seconds and %f milliseconds');
        $result = [
            "time" => [
                "start" => number_format(self::$time, 6) . " ms",
                "stop" => number_format($timeStop, 6) . " ms",
                "diff" => number_format($finalTime, 6) . " ms",
                "timestampStart" => $timestampStart,
                "timestampStop" => $timestampStop,
                "timestampDiff" => $timestampDiff
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
