<?php

namespace Scipper\ApplicationServer\Stream\Input;

/**
 * Interface InputEventListenerInterface
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\Stream\Input
 * @package Scipper\ApplicationServer\Stream\Input
 */
interface InputEventListenerInterface {

    /**
     *
     * @return boolean
     */
    public function listen();

    /**
     * @return string
     */
    public function getStream();

    /**
     *
     */
    public function reset();

}