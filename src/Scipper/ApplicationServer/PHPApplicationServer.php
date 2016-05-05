<?php

namespace Scipper\ApplicationServer;

use RIP\RESTInPHP;
use Scipper\ApplicationServer\Management\ManagementLoader;
use Scipper\ApplicationServer\Network\Request;
use Scipper\ApplicationServer\Network\Socket\ClientThread;
use Scipper\ApplicationServer\Network\Socket\ServerSocket;
use Scipper\ApplicationServer\Network\Socket\SocketList;
use Scipper\ApplicationServer\System\Process\SignalHandler;
use Scipper\ApplicationServer\Tools\Performance\Timer;

/**
 * Class PHPAppicationServer
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer
 * @package Scipper\ApplicationServer
 */
class PHPApplicationServer {

    /**
     * @var SignalHandler
     */
    protected $signalHandler;

    /**
     * @var boolean
     */
    protected $running;

    /**
     * @var SocketList
     */
    protected $socketList;

    /**
     * @var ServerSocket
     */
    protected $managementSocket;

    /**
     * @var array
     */
    protected $managementClients;

    /**
     * @var array
     */
    protected $managementClientThreads;

    /**
     * @var ManagementLoader
     */
    protected $managementLoader;

    /**
     * @var RESTInPHP
     */
    protected $managementServer;


    /**
     * PHPApplicationServer constructor.
     *
     * @param SignalHandler $signalHandler
     * @param ServerSocket $managementSocket
     * @param SocketList $socketList
     * @param ManagementLoader $managementLoader
     */
    public function __construct(SignalHandler $signalHandler, ServerSocket $managementSocket, SocketList $socketList, ManagementLoader $managementLoader) {
        $this->signalHandler = $signalHandler;
        $this->running = false;
        $this->managementSocket = $managementSocket;
        $this->managementClients = array();
        $this->managementClientThreads = array();
        $this->socketList = $socketList;
        $this->managementLoader = $managementLoader;
        $this->managementServer = null;
    }

    /**
     *
     */
    public function boot() {
        register_shutdown_function(array($this, "destroy"));

        $this->managementSocket->open();
        $this->managementServer = $this->managementLoader->getManagementServerInstance();
        $this->managementServer->boot();
    }

    /**
     * @throws Network\Socket\Exceptions\NoResourceException
     */
    public function run() {
        $timer = new Timer();

        $this->running = true;

        while($this->running) {
            $timer->update();

            if($this->signalHandler->getSignal() == SignalHandler::SIGNAL_HANDLER_SHUTDOWN) {
                $this->running = false;
                break;
            }

            $this->socketList->tryNewSocket($this->managementSocket->waitForConnection());

            foreach($this->socketList as $k => $clientSocket) {
                $socketRead = socket_read($clientSocket, 2048);
                if($socketRead === false) {
                    continue;
                }

                $request = new Request();
                $request->withHeaderString($socketRead);

                $this->managementServer->processRequest($request->getUri(), $request->getMethod());
                ob_start();
                $this->managementServer->prepareResponse();
                $response = ob_get_clean();
                @socket_write($clientSocket, $response, strlen($response));
                $this->managementClientThreads[$k] = new ClientThread($clientSocket, $request);
                //$this->managementClientThreads[$k]->start();

            }

            foreach($this->managementClientThreads as $k => $clientThread) {
                $this->socketList->close($clientThread->getSocket());
                unset($this->managementClientThreads[$k]);
            }


            echo $timer->getElapsed() . "\r";

            //wait until 1 ms is over
            //performance tweek, DO NOT REMOVE
            $timer->adjust();
        }
    }

    /**
     *
     */
    public function shutdown() {
        echo "shutdown" . PHP_EOL;

        posix_kill(posix_getpid(), SIGUSR1);
    }

    /**
     *
     */
    public function destroy() {
        $this->managementSocket->destroy();

        echo "destroy" . PHP_EOL;
    }

}