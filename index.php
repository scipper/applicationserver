<?php

require_once 'vendor/autoload.php';

use Scipper\ApplicationServer\Management\ManagementLoader;
use Scipper\ApplicationServer\Network\Socket\ServerSocket;
use Scipper\ApplicationServer\Network\Socket\SocketList;
use Scipper\ApplicationServer\PHPApplicationServer;
use Scipper\ApplicationServer\System\Process\SignalHandler;
use Scipper\Classloader\Classloader;

$classloader = new Classloader(dirname(__FILE__) . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR);
$classloader->register();

$address = '127.0.0.1';
$port = 3000;

$signalHandler = new SignalHandler();
$managementSocket = new ServerSocket($address, (++$port));
$socketList = new SocketList();
$managementLoader = new ManagementLoader();

$phpas = new PHPApplicationServer($signalHandler, $managementSocket, $socketList, $managementLoader);
$phpas->boot();
$phpas->run();
$phpas->shutdown();