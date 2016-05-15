<?php

namespace Scipper\ApplicationServer\Stream\Input;

/**
 * Interface KeyMapperInterface
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\Stream\Input
 * @package Scipper\ApplicationServer\Stream\Input
 */
interface KeyMapperInterface {

    /**
     * KeyMapperInterface constructor.
     *
     * @param string $key
     */
    public function __construct($key);

    /**
     *
     * @return string
     */
    public function getKey();

}