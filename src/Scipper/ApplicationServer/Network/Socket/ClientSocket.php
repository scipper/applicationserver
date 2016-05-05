<?php

namespace Scipper\ApplicationServer\Network\Socket;

use Scipper\ApplicationServer\Network\Socket\Exceptions\NoResourceException;

/**
 * Class ClientSocket
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\Network\Socket
 * @package Scipper\ApplicationServer\Network\Socket
 */
class ClientSocket {

    /**
     * @var resource
     */
    protected $socket;


    /**
     * ClientSocket constructor.
     *
     * @param $socket
     *
     * @throws NoResourceException
     */
    public function __construct($socket) {
        if(!is_resource($socket)) {
            throw new NoResourceException('The given object is no resource.');
        }
        socket_set_nonblock($socket);
        $this->socket = $socket;
    }

    /**
     * @return resource
     */
    public function getSocket() {
        return $this->socket;
    }

}