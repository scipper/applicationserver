<?php

include "Timer.php";
include "ClientThread.php";
include "Request.php";



$running = true;


$iter = 0;

$timer = new Timer();

try {
    $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if (!$socket) {
        throw new \Exception('socket could not be created: ' . '(' . socket_last_error($socket) . ') ' . socket_strerror(socket_last_error($socket)));
    }

    $socket_bind = @socket_bind($socket, '127.0.0.1', 3001);
    if (!$socket_bind) {
        throw new \Exception('socket could not be bound: ' . '(' . socket_last_error($socket) . ') ' . socket_strerror(socket_last_error($socket)));
    }

    $socket_listen = socket_listen($socket);
    if (!$socket_listen) {
        throw new \Exception('socket could not be listened: ' . '(' . socket_last_error($socket) . ') ' . socket_strerror(socket_last_error($socket)));
    }


    $socket_set_nonblock = socket_set_nonblock($socket);
    if (!$socket_set_nonblock) {
        throw new \Exception('something went wront setting the socket NON BLOCKING: ' . '(' . socket_last_error($socket) . ') ' . socket_strerror(socket_last_error($socket)));
    }

} catch(\Exception $e) {
    echo 'Startup Error: ' . PHP_EOL;
    echo $e->getMessage() . PHP_EOL;

    die();
}

echo "Server started successfully!" . PHP_EOL;
echo "Start listening for incoming clients..." . PHP_EOL;

$clients = array();

/**
 * @var ClientThread[] $clientThreads
 */
$clientThreads = array();

while($running) {

    $connection = @socket_accept($socket);
    if($connection) {
        if(!in_array($connection, $clients)) {
            socket_set_nonblock($connection);
            $clients[] = $connection;
            echo "client connected: " . "(" . $connection . ") " . count($clients) . PHP_EOL;
        }
    }

    $timer->update();

    foreach($clients as $k => $client) {
        $socket_read = socket_read($client, 2048);
        if ($socket_read === false) {
            continue;
        }

        $request = new Request();
        $request->withHeaderString($socket_read);
        $clientThreads[$k] = new ClientThread($client, $request);
        $clientThreads[$k]->start();

    }

    foreach($clientThreads as $k => $clientThread) {
        socket_close($clientThread->getSocket());
        unset($clients[$k]);
        unset($clientThreads[$k]);
    }

    //$iter++;

    /*if($iter >= 10000000) {
        $running = false;
    }*/

}

foreach($clients as $client) {
    socket_close($client);
}

socket_close($socket);

echo "stopped" . PHP_EOL;
