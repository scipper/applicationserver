<?php

namespace Scipper\ApplicationServer\Network\Listener;

/**
 * Class Listener
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\Network\Listener
 * @package Scipper\ApplicationServer\Network\Listener
 */
class Listener {

    /**
     * @var resource
     */
    protected $socket;

    /**
     * @var string
     */
    protected $address;

    /**
     * @var integer
     */
    protected $port;


    /**
     * Listener constructor.
     *
     * @param string $address
     * @param int $port
     */
    public function __construct($address = '127.0.0.1', $port = 3000) {
        $this->socket = null;
        $this->address = $address;
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getAddress() {
        return (string) $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address) {
        $this->address = (string) $address;
    }

    /**
     * @return int
     */
    public function getPort() {
        return (int) $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort($port) {
        $this->port = (int) $port;
    }

    /**
     * @throws \Exception
     */
    public function open() {
        $this->create();
        $this->bind();
        $this->listen();
        $this->setNonBlocking();
    }

    /**
     * @param int $domain
     * @param int $type
     * @param int $protocol
     *
     * @throws \Exception
     */
    public function create($domain = AF_INET, $type = SOCK_STREAM, $protocol = SOL_TCP) {
        $this->socket = @socket_create($domain, $type, $protocol);
        if (!$this->socket) {
            throw new \Exception('socket could not be created: ' . '(' . socket_last_error($this->socket) . ') ' . socket_strerror(socket_last_error($this->socket)));
        }
    }

    /**
     * @throws \Exception
     */
    public function bind() {
        $socketBind = @socket_bind($this->socket, $this->address, $this->port);
        if (!$socketBind) {
            throw new \Exception('socket could not be bound: ' . '(' . socket_last_error($this->socket) . ') ' . socket_strerror(socket_last_error($this->socket)));
        }
    }

    /**
     * @throws \Exception
     */
    public function listen() {
        $socketListen = socket_listen($this->socket);
        if (!$socketListen) {
            throw new \Exception('socket could not be listened: ' . '(' . socket_last_error($this->socket) . ') ' . socket_strerror(socket_last_error($this->socket)));
        }
    }

    /**
     * @throws \Exception
     */
    public function setNonBlocking() {
        $socketSetNonblock = socket_set_nonblock($this->socket);
        if (!$socketSetNonblock) {
            throw new \Exception('something went wront setting the socket NON BLOCKING: ' . '(' . socket_last_error($this->socket) . ') ' . socket_strerror(socket_last_error($this->socket)));
        }
    }

    /**
     * @return mixed
     */
    public function waitForConnection() {
        return socket_accept($this->socket);
    }

    /**
     * @return null|resource
     */
    public function getSocket() {
        return $this->socket;
    }

    /**
     *
     */
    public function destroy() {
        socket_close($this->socket);
    }

}