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
     * @param MessageQueue $messageQueue
     * @param float $provideInterval
     */
    public function __construct(MessageQueue $messageQueue, $provideInterval = .01) {
        $this->messageQueue = $messageQueue;
        $this->provideInterval = $provideInterval;
        $this->timer = 0.0;
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
     * 
     */
    public function publishAll() {
        $messagesRead = 0;
        while(($message = $this->messageQueue->getFirstMessage()) !== null && $messagesRead < 100) {
            $messagesRead++;
            echo $message;
        }
    }

    /**
     * @param Message $message
     */
    public function addMessage(Message $message) {
        $this->messageQueue->addMessage($message);
    }

}