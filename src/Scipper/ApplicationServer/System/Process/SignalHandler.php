<?php

namespace Scipper\ApplicationServer\System\Process;

/**
 * Class SignalHandler
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\System\Process
 * @package Scipper\ApplicationServer\System\Process
 */
class SignalHandler {

    const SIGNAL_HANDLER_OK = 0;
    const SIGNAL_HANDLER_SHUTDOWN = 100;


    /**
     * @var integer
     */
    protected $signal;


    /**
     * SignalHandler constructor.
     */
    public function __construct() {
        $this->signal = self::SIGNAL_HANDLER_OK;

        declare(ticks = 1);

        pcntl_signal(SIGTERM, array($this, "handle"));
        pcntl_signal(SIGHUP, array($this, "handle"));
        pcntl_signal(SIGUSR1, array($this, "handle"));
        pcntl_signal(SIGINT, array($this, "handle"));
    }

    /**
     * @return int
     */
    public function getSignal() {
        return $this->signal;
    }

    /**
     * @param $signal
     */
    public function handle($signal) {

        switch ($signal) {
            case SIGTERM:
                echo "exit" . PHP_EOL;
                break;
            case SIGINT:
                echo "CTRL + C" . PHP_EOL;
                break;
            case SIGHUP:
                echo "reboot" . PHP_EOL;
                break;
            case SIGUSR1:
                echo "Caught SIGUSR1...\n";
                break;
            default:
                echo "something else" . PHP_EOL;
        }

        $this->signal = self::SIGNAL_HANDLER_SHUTDOWN;

    }

}