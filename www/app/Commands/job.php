<?php

echo PHP_EOL;
for ($i = 1; $i <= 30; $i++) { 
    echo $i, " | ", (new \DateTime())->format("Y-m-d H:i:s"), PHP_EOL;
    sleep(1);
}
