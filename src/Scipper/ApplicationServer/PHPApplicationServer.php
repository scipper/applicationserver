<?php

namespace Scipper\ApplicationServer;

use Scipper\ApplicationServer\Network\Listener\ListenerManager;
use Scipper\ApplicationServer\Network\Request;
use Scipper\ApplicationServer\Stream\Input\InputManager;
use Scipper\ApplicationServer\Stream\Input\StdEventListener;
use Scipper\ApplicationServer\Stream\Output\Message;
use Scipper\ApplicationServer\Stream\Output\MessageProvider;
use Scipper\ApplicationServer\Stream\Output\MessageQueue;
use Scipper\ApplicationServer\System\CommandLine\CommandLine;
use Scipper\ApplicationServer\System\Process\SignalHandler;
use Scipper\ApplicationServer\Tools\Performance\Timer;
use Scipper\Colorizer\Colorizer;
use Symfony\Component\Console\Input\Input;

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
     * @var CommandLine
     */
    protected $commandLine;


    /**
     * PHPApplicationServer constructor.
     */
    public function __construct() {
        $this->running = false;

        $this->signalHandler = new SignalHandler();
        $this->colorizer = new Colorizer();
        $this->messageProvider = new MessageProvider(new MessageQueue());
        $this->listenerManager = new ListenerManager($this->messageProvider);
        $this->systemStartTime = time();
        $this->commandLine = new CommandLine($this, $this->messageProvider, new InputManager(new StdEventListener()), $this->colorizer);
    }

    /**
     *
     */
    public function boot() {
        $this->messageProvider->addMessage((new Message($this->colorizer))->setPromtMessage(""));
        $this->messageProvider->addMessage((new Message($this->colorizer))->setPromtMessage(""));

        $this->messageProvider->addMessage((new Message($this->colorizer))->setPromtMessage("Welcome to  "));
        $this->messageProvider->addMessage((new Message($this->colorizer))->setPromtMessage("PHP Application Server ", Colorizer::FG_GREEN));
        $this->messageProvider->addMessage((new Message($this->colorizer))->setPromtMessage(""));
        $this->messageProvider->addMessage((new Message($this->colorizer))->setPromtMessage(""));

        $this->messageProvider->addMessage((new Message($this->colorizer))->setPromtMessage("Booting ..."));
        $this->messageProvider->addMessage((new Message($this->colorizer))->setPromtMessage(""));

        $this->messageProvider->publishAll();

        /*
        $this->inputManager->addMapping("quit", function() {
            $this->running = false;
        });
        $this->inputManager->addMapping("add listener", function() {
            $this->listenerManager->addListener("127.0.0.1", 3000);
            $this->listenerManager->startListener("127.0.0.1", 3000);
        });
        */

        $this->commandLine->initializeCommands();
        $this->commandLine->assignToInputManager();

        register_shutdown_function(array($this, "shutdown"));

    }

    /**
     * @throws Network\Socket\Exceptions\NoResourceException
     */
    public function run() {
        $this->messageProvider->addMessage((new Message($this->colorizer))->setPromtMessage(""));
        $this->messageProvider->addMessage((new Message($this->colorizer))->setPromtMessage("PHP Application Server is up and running"));

        $this->messageProvider->addMessage((new Message($this->colorizer))->setPromtInput());

        $this->messageProvider->publishAll();

        $timer = new Timer();
        $timerCount = 0.0;

        $this->running = true;

        while($this->running) {
            $timer->update();
            $this->messageProvider->update($timer->getElapsed());
            $this->messageProvider->getFirstMessage();

            $this->commandLine->listen($timer->getElapsed());

            $timerCount += $timer->getElapsed();

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

            /*
            if($timerCount >= 1) {
                $timerCount -= 1;


                $this->messageProvider->addMessage(new Message($this->colorizer, $this->colorizer->linesUp(6)));

                $this->messageProvider->addMessage(
                    (new Message($this->colorizer))->setPromtMessage("")
                );

                //Latency
                $this->messageProvider->addMessage(
                    (new Message($this->colorizer))->setKeyValueCombinesMessage("Latency: ", $timer->getAverageTimePerTick())
                );

                //Systemtime
                $this->messageProvider->addMessage(
                    (new Message($this->colorizer))->setKeyValueCombinesMessage("System time: ", date("Y-m-d H:i:s"))
                );

                //Uptime
                $this->messageProvider->addMessage(
                    (new Message($this->colorizer))->setKeyValueCombinesMessage("Uptime: ", gmdate("H:i:s", time() - $this->systemStartTime))
                );

                //System Load
                $this->messageProvider->addMessage(
                    (new Message($this->colorizer))->setKeyValueCombinesMessage("System Load: ", $this->loadMonitor->getServerLoad())
                );

            }
            */

            //wait until 1 ms is over
            //performance tweak, DO NOT REMOVE
            $timer->adjust();
        }
    }

    /**
     *
     */
    public function stop() {
        $this->running = false;
    }

    /**
     *
     */
    public function shutdown() {
        $this->messageProvider->addMessage(new Message($this->colorizer, $this->colorizer->linesDown(6)));

        $this->messageProvider->addMessage((new Message($this->colorizer))->setPromtMessage("Shutting down ...", Colorizer::FG_ORANGE));
        $this->messageProvider->publishAll();

        $this->listenerManager->shutdown();

        posix_kill(posix_getpid(), SIGUSR1);
    }

    /**
     *
     */
    public function __destruct() {
        $this->messageProvider->addMessage((new Message($this->colorizer))->setPromtMessage("Destructor called ...", Colorizer::FG_ORANGE));
        $this->messageProvider->publishAll();

        $this->shutdown();
    }

}