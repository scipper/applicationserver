<?php

include "Timer.php";

/*declare(ticks = 1);

function tickFunction() {

}

register_tick_function("tickFunction");
*/


$running = true;

$iter = 0;

$timer = new Timer();


while($running) {

    $iter++;
    $timer->update();

    if($iter >= 10000000) {
        $running = false;
    }

}

echo $timer->getElapsed() . PHP_EOL;