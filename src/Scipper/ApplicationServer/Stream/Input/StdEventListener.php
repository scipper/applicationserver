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
     * @var KeyMapperInterface
     */
    protected $key;

    /**
     *
     * @var array
     */
    protected $read;

    /**
     *
     * @var array
     */
    protected $write;

    /**
     *
     * @var array
     */
    protected $except;

    /**
     *
     * @var integer
     */
    protected $streamSelectResult;

    /**
     *
     */
    public function __construct() {
        stream_set_blocking(STDIN, 0);
        readline_callback_handler_install('', function() { });
    }

    /**
     *
     * @return boolean
     */
    public function listen() {
        $this->read = array(STDIN);
        $this->write = NULL;
        $this->except = NULL;
        $this->streamSelectResult = stream_select($this->read, $this->write, $this->except, 0);
        if($this->streamSelectResult && in_array(STDIN, $this->read)) {
            $c = stream_get_contents(STDIN, 1024);
            $value = unpack('H*', strtolower($c));

            $this->setKey(new StdInKeys($value[1]));

            return true;
        }

        return false;
    }

    /**
     *
     * @param KeyMapperInterface $key
     */
    public function setKey(KeyMapperInterface $key) {
        $this->key = $key;
    }

    /**
     *
     * @return KeyMapperInterface
     */
    public function getKey() {
        return $this->key;
    }

    /**
     *
     */
    public function reset() {
        $this->key = NULL;
    }

}