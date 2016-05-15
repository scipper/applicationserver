<?php

namespace Scipper\ApplicationServer;

use Scipper\ApplicationServer\Network\Listener\ListenerManager;
use Scipper\ApplicationServer\Network\Request;
use Scipper\ApplicationServer\Stream\Output\Message;
use Scipper\ApplicationServer\Stream\Output\MessageProvider;
use Scipper\ApplicationServer\Stream\Output\MessageQueue;
use Scipper\ApplicationServer\System\Process\SignalHandler;
use Scipper\ApplicationServer\Tools\Performance\LoadMonitor;
use Scipper\ApplicationServer\Tools\Performance\Timer;
use Scipper\Colorizer\Colorizer;

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
     * @var boolean
     */
    protected $running;

    /**
     * @var SignalHandler
     */
    protected $signalHandler;

    /**
     * @var Colorizer
     */
    protected $colorizer;

    /**
     * @var MessageProvider
     */
    protected $messageProvider;

    /**
     * @var ListenerManager
     */
    protected $listenerManager;

    /**
     * @var integer
     */
    protected $systemStartTime;

    /**
     * @var LoadMonitor
     */
    protected $loadMonitor;


    /**
     * PHPApplicationServer constructor.
     */
    public function __construct() {
        $this->running = false;

        $this->signalHandler = new SignalHandler();
        $this->colorizer = new Colorizer();
        $this->messageProvider = new MessageProvider($this->colorizer, new MessageQueue());
        $this->listenerManager = new ListenerManager($this->messageProvider);
        $this->systemStartTime = time();
        $this->loadMonitor = new LoadMonitor();
    }

    /**
     *
     */
    public function boot() {
        $this->messageProvider->getWelcomeMessage();
        $this->messageProvider->getBootMessage();

        register_shutdown_function(array($this, "shutdown"));

    }

    /**
     * @throws Network\Socket\Exceptions\NoResourceException
     */
    public function run() {
        $this->messageProvider->getReadyMessage();

        $timer = new Timer();
        $timerCount = 0.0;

        $this->running = true;

        //stream_set_blocking(STDIN, 0);

        while($this->running) {
            $timer->update();
            $this->messageProvider->update($timer->getElapsed());

            $this->messageProvider->getFirstMessage();

            $timerCount += $timer->getElapsed();
            //$c = stream_get_contents(STDIN);

            if($this->signalHandler->getSignal() == SignalHandler::SIGNAL_HANDLER_SHUTDOWN) {
                $this->running = false;
                break;
            }

            /*
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
            */

            if($timerCount >= 1) {
                $timerCount -= 1;

                $this->messageProvider->addMessage(new Message($this->colorizer, ""));
                $this->messageProvider->getFirstMessage(true);

                $message = new Message($this->colorizer);

                //Latency
                $message->setKeyValueCombinesMessage("Latency: ", $timer->getAverageTimePerTick());
                $this->messageProvider->addMessage($message);
                $this->messageProvider->getFirstMessage(true);

                //Systemtime
                $message->setKeyValueCombinesMessage("System time: ", date("Y-m-d H:i:s"));
                $this->messageProvider->addMessage($message);
                $this->messageProvider->getFirstMessage(true);

                //Uptime
                $message->setKeyValueCombinesMessage("Uptime: ", gmdate("H:i:s", time() - $this->systemStartTime));
                $this->messageProvider->addMessage($message);
                $this->messageProvider->getFirstMessage(true);

                //System Load
                $message->setKeyValueCombinesMessage("System Load: ", $this->loadMonitor->getServerLoad());
                $this->messageProvider->addMessage($message);
                $this->messageProvider->getFirstMessage(true);

                $this->messageProvider->addMessage(new Message($this->colorizer, $this->colorizer->linesUp(6)));
                $this->messageProvider->getFirstMessage(true);
            }

            //wait until 1 ms is over
            //performance tweak, DO NOT REMOVE
            $timer->adjust();
        }
    }

    /**
     *
     */
    public function shutdown() {
        $this->messageProvider->addMessage(new Message($this->colorizer, $this->colorizer->linesDown(6)));
        $this->messageProvider->getFirstMessage(true);

        $msg = new Message($this->colorizer);
        $msg->setMessage("Shutting down ...", Colorizer::FG_ORANGE);
        $this->messageProvider->addMessage($msg);
        $this->messageProvider->getFirstMessage(true);

        $this->listenerManager->shutdown();

        posix_kill(posix_getpid(), SIGUSR1);
    }

    /**
     *
     */
    public function __destruct() {

        $msg = new Message($this->colorizer);
        $msg->setMessage("Destructor called ...", Colorizer::FG_ORANGE);
        $this->messageProvider->addMessage($msg);
        $this->messageProvider->getFirstMessage(true);

        $this->shutdown();
    }

}