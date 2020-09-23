<?php

namespace math;

function add(float ...$nums) :float
{
    $result = 0;
    foreach ($nums as $key => $value) {
        $result += $value;
        unset($nums[$key]);
        unset($key);
        unset($value);
    }
    return $result;
}
