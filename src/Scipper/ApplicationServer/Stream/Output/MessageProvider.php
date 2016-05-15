<?php

namespace Scipper\ApplicationServer\Stream\Output;

use Scipper\Colorizer\Colorizer;

/**
 * Class MessageProvider
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\Stream\Output
 * @package Scipper\ApplicationServer\Stream\Output
 */
class MessageProvider {

    /**
     * @var Colorizer
     */
    protected $colorizer;

    /**
     * @var array
     */
    protected $messageQueue;

    /**
     * @var float
     */
    protected $provideInterval;

    /**
     * @var float
     */
    protected $timer;

    /**
     * @var string
     */
    protected $separator;


    /**
     * MessageProvider constructor.
     *
     * @param Colorizer $colorizer
     * @param MessageQueue $messageQueue
     * @param int $provideInterval
     */
    public function __construct(Colorizer $colorizer, MessageQueue $messageQueue, $provideInterval = 1) {
        $this->colorizer = $colorizer;
        $this->messageQueue = $messageQueue;
        $this->provideInterval = $provideInterval;
        $this->timer = 0.0;
        $this->separator = "___________________________________";
    }

    /**
     * @param float $tpf
     */
    public function update($tpf) {
        $this->timer += $tpf;
    }

    /**
     * @param bool $instant
     */
    public function getFirstMessage($instant = false) {
        if($this->timer < $this->provideInterval && !$instant) {
            return;
        } elseif($this->timer >= $this->provideInterval && !$instant) {
            $this->timer -= $this->provideInterval;
        }

        $firstMessage = $this->messageQueue->getFirstMessage();
        if(!is_null($firstMessage)) {
            echo $firstMessage;
        }
    }

    /**
     * @param Message $message
     */
    public function addMessage(Message $message) {
        $this->messageQueue->addMessage($message);
    }

    /**
     * @param $msg
     * @param string $customColor
     */
    public function getCustomMessage($msg, $customColor = Colorizer::FG_CYAN, $linebreak = true) {
        $this->colorizer->cecho($msg, $customColor);
        if($linebreak) {
            echo PHP_EOL;
        }
    }

    /**
     * @param $msg
     * @param string $customColor
     */
    public function getCustomPromtMessage($msg, $customColor = Colorizer::FG_CYAN, $linebreak = true) {
        $this->colorizer->cecho("| ", Colorizer::FG_CYAN);
        $this->getCustomMessage($msg, $customColor, $linebreak);
    }

    /**
     *
     */
    public function getWelcomeMessage() {
        $this->getCustomMessage("");
        $this->getCustomMessage("");
        $this->getCustomPromtMessage("Welcome to ");
        $this->getCustomPromtMessage("PHP Application Server ", Colorizer::FG_GREEN);
        $this->getCustomPromtMessage($this->separator);
        $this->getCustomPromtMessage("");
    }

    /**
     *
     */
    public function getPromt() {
        $this->colorizer->cecho("> ", Colorizer::FG_CYAN);
    }

    /**
     *
     */
    public function getBootMessage() {
        $this->getCustomPromtMessage("Booting ...");
        $this->getCustomPromtMessage("");
    }

    /**
     *
     */
    public function getReadyMessage() {
        $this->getCustomPromtMessage($this->separator);
        $this->getCustomPromtMessage("");
        $this->getCustomPromtMessage("PHP Application Server is up and running");
        $this->getCustomPromtMessage("");
    }

}