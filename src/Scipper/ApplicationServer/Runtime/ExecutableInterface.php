<?php

namespace Scipper\ApplicationServer\Runtime;

/**
 * Interface ExecutableInterface
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\Runtime
 * @package Scipper\ApplicationServer\Runtime
 */
interface ExecutableInterface {

    /**
     * @return boolean
     */
    public function init();

    /**
     * @param $url
     * @param $method
     *
     * @return boolean
     */
    public function handleRequest($url, $method);

    /**
     * @return boolean
     */
    public function execute();

    /**
     * @return string
     */
    public function getResponse();

    /**
     * @return boolean
     */
    public function shutdown();

}