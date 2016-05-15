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
     *
     * @param KeyMapperInterface $key
     */
    public function setKey(KeyMapperInterface $key);

    /**
     *
     * @return KeyMapperInterface
     */
    public function getKey();

    /**
     *
     */
    public function reset();

}