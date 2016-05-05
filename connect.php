<?php

try {
    $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if (!$socket) {
        throw new \Exception('socket could not be created: ' . '(' . socket_last_error($socket) . ') ' . socket_strerror(socket_last_error($socket)));
    }

    $socket_connect = @socket_connect($socket, '127.0.0.1', 3001);
    if (!$socket_connect) {
        throw new \Exception('socket could not be connected: ' . '(' . socket_last_error($socket) . ') ' . socket_strerror(socket_last_error($socket)));
    }
} catch(\Exception $e) {
    echo 'Connection Error: ' . PHP_EOL;
    echo $e->getMessage() . PHP_EOL;

    die();
}


$running = true;
$iter = 0;

while($running) {

    $buf = "";
    $socket_recv = @socket_recv($socket, $buf, 8192, MSG_DONTWAIT);

    $msg = "\r";
    if($result = @socket_write($socket, $msg, strlen($msg)) === false) {
        @socket_close($socket);

        $running = false;
    }

    $iter++;

    if($iter >= 10000000) {
        $running = false;
    }

    usleep(1000000);
}


@socket_close($socket);