<?php

namespace Scipper\ApplicationServer\Network\Listener;

use Scipper\ApplicationServer\Network\Listener\Exceptions\AuthorityAlreadyInUse;
use Scipper\ApplicationServer\Network\Listener\Exceptions\AuthorityNotFound;
use Scipper\ApplicationServer\Stream\Output\MessageProvider;
use Scipper\Colorizer\Colorizer;

/**
 * Class ListenerManager
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\Network\Listener
 * @package Scipper\ApplicationServer\Network\Listener
 */
class ListenerManager {

    /**
     * @var Listener[]
     */
    protected $listener;

    /**
     * @var MessageProvider
     */
    protected $ssmp;


    /**
     * ListenerManager constructor.
     */
    public function __construct(MessageProvider $ssmp) {
        $this->listener = array();
        $this->ssmp = $ssmp;
    }

    /**
     * @param string $address
     * @param int $port
     * @param bool $openInstant
     *
     * @throws AuthorityAlreadyInUse
     * @throws AuthorityNotFound
     */
    public function addListener($address = "127.0.0.1", $port = 3000, $openInstant = false) {
        $authority = $address . $port;
        if(isset($this->listener[$authority])) {
            throw new AuthorityAlreadyInUse();
        }

        $this->listener[$authority] = new Listener($address, $port);

        $this->ssmp->getCustomMessage('Listener for authority "' . $authority . '" successfully added to ListenerManager.', Colorizer::FG_GREEN);
        if($openInstant) {
            $this->startListener($address, $port);
        }
    }

    /**
     * @param $address
     * @param $port
     *
     * @throws AuthorityNotFound
     */
    public function startListener($address, $port) {
        $authority = $address . $port;
        if(!isset($this->listener[$authority])) {
            throw new AuthorityNotFound();
        }

        $this->listener[$authority]->open();
        $this->ssmp->getCustomMessage('Listener for authority "' . $authority . '" is now open.', Colorizer::FG_GREEN);
    }

    /**
     * 
     */
    public function shutdown() {
        foreach($this->listener as $listener) {
            $listener->destroy();
        }
    }

}