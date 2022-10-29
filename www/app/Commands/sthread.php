<?php

for ($i = 1; $i <= 10; $i++) { 
    sleep(rand(1, 3));
    echo "Result: ", $i, PHP_EOL;
}
