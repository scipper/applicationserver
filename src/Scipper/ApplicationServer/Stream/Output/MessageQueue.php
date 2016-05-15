<?php

namespace Scipper\ApplicationServer\Stream\Output;

/**
 * Class MessageQueue
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\Stream\Output
 * @package Scipper\ApplicationServer\Stream\Output
 */
class MessageQueue {

    /**
     * @var Message[]
     */
    protected $messages;


    /**
     * MessageQueue constructor.
     */
    public function __construct() {
        $this->messages = array();
    }

    /**
     * @param Message $message
     */
    public function addMessage(Message $message) {
        array_push($this->messages, $message);
    }

    /**
     * @return Message
     */
    public function getFirstMessage() {
        return array_shift($this->messages);
    }

    /**
     * @return Message
     */
    public function getLastMessage() {
        return array_pop($this->messages);
    }

}