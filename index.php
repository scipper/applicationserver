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

$phpas = new PHPApplicationServer();
$phpas->boot();
$phpas->run();
$phpas->shutdown();