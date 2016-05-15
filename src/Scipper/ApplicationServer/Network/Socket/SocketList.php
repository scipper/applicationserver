<?php

namespace Scipper\ApplicationServer\Network\Socket;

use Scipper\ApplicationServer\Network\Socket\Exceptions\AlreadyConnectedException;
use Scipper\ApplicationServer\Network\Socket\Exceptions\NoResourceException;

/**
 * Class SocketList
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\Network\Socket
 * @package Scipper\ApplicationServer\Network\Socket
 */
class SocketList implements \Iterator {

    /**
     * @var resource[]
     */
    protected $socketList;

    /**
     * @var ClientSocket[]
     */
    protected $clientSocketList;


    /**
     * SocketList constructor.
     */
    public function __construct() {
        $this->socketList = array();
        $this->clientSocketList = array();
    }

    /**
     * @param $socket
     *
     * @return boolean
     *
     * @throws AlreadyConnectedException
     * @throws NoResourceException
     */
    public function tryNewSocket($socket) {
        if($socket === false) {
            return false;
        }

        $this->add($socket);

        return true;
    }

    /**
     * @param $socket
     *
     * @throws AlreadyConnectedException
     * @throws NoResourceException
     */
    public function add($socket) {
        if(!is_resource($socket)) {
            throw new NoResourceException('The given object is no resource.');
        }

        if(isset($this->socketList[(string) $socket])) {
            throw new AlreadyConnectedException('The resource \'$socket\' is already connected.');
        }

        $clientSocket = new ClientSocket($socket);

        $this->socketList[(string) $socket] = $socket;
        $this->clientSocketList[(string) $socket] = $clientSocket;
    }

    /**
     * @return array|\resource[]
     */
    public function getSocketList() {
        return $this->socketList;
    }

    /**
     * @return array|ClientSocket[]
     */
    public function getClientSocketList() {
        return $this->clientSocketList;
    }

    /**
     * @param $socket
     *
     * @throws NoResourceException
     */
    public function remove($socket) {
        if(!is_resource($socket)) {
            throw new NoResourceException('The given object is no resource.');
        }

        if(isset($this->socketList[(string) $socket])) {
            unset($this->socketList[(string) $socket]);
            unset($this->clientSocketList[(string) $socket]);
        }
    }

    /**
     * @param $socket
     *
     * @throws NoResourceException
     */
    public function close($socket) {
        if(!is_resource($socket)) {
            throw new NoResourceException('The given object is no resource.');
        }

        if(isset($this->socketList[(string) $socket])) {
            socket_close($socket);
            unset($this->socketList[(string) $socket]);
            unset($this->clientSocketList[(string) $socket]);
        }
    }

    /**
     * @return mixed
     */
    public function current() {
        return current($this->socketList);
    }

    /**
     * @return mixed
     */
    public function next() {
        return next($this->socketList);
    }

    /**
     * @return mixed
     */
    public function key() {
        return key($this->socketList);
    }

    /**
     * @return bool
     */
    public function valid() {
        return key($this->socketList) !== null;
    }

    /**
     * @return mixed
     */
    public function rewind() {
        return reset($this->socketList);
    }

}