<?php

namespace Scipper\ApplicationServer\Stream\Input;

/**
 * Class StdEventListener
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\Stream\Input
 * @package Scipper\ApplicationServer\Stream\Input
 */
class StdEventListener implements InputEventListenerInterface {

    /**
     *
     * @var string
     */
    protected $stream;

    /**
     *
     */
    public function __construct() {
        stream_set_blocking(STDIN, 0);
    }

    /**
     *
     * @return boolean
     */
    public function listen() {
        $stream = stream_get_contents(STDIN);
        if(!is_null($stream) && !empty($stream)) {
            $this->stream = trim($stream);
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getStream() {
        return $this->stream;
    }

    /**
     *
     */
    public function reset() {
        $this->stream = NULL;
    }

}