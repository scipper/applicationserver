<?php

namespace Scipper\ApplicationServer\System\CommandLine;

/**
 * Interface CommandInterface
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\System\CommandLine
 * @package Scipper\ApplicationServer\System\CommandLine
 */
interface CommandInterface {

    /**
     * @return string
     */
    public function name();

    /**
     * @return mixed
     */
    public function execute();

}