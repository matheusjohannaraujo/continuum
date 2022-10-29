<?php

for ($i = 1; $i <= 10; $i++) { 
    async(function() use ($i) {
        sleep(rand(1, 3));
        return $i;
    })->then(function($value) {
        echo "Result: ", $value, PHP_EOL;
    });
}
