<?php

namespace Scipper\ApplicationServer\Network\Socket;

use Scipper\ApplicationServer\Network\Request;

/**
 * Class ClientThread
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\Network\Socket
 * @package Scipper\ApplicationServer\Network\Socket
 */
class ClientThread {

    /**
     * @var resource
     */
    protected $socket;

    /**
     * @var Request
     */
    protected $request;


    /**
     * ClientThread constructor.
     *
     * @param $socket
     * @param $request
     */
    public function __construct($socket, $request) {
        $this->socket = $socket;
        $this->request = $request;
    }

    /**
     * wrapper for threading
     */
    public function start() {
        $this->run();
    }

    /**
     * 
     */
    public function run() {
        $in = "HTTP/1.1 200 OK\r\n";
        $in .= "Write: YOUR DID IT\r\n";
        $in .= "Connection: Close\r\n\r\n";
        @socket_write($this->socket, $in, strlen($in));
    }

    /**
     * @return resource
     */
    public function getSocket() {
        return $this->socket;
    }

}