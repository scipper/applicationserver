<?php

namespace Scipper\ApplicationServer\System\Commandline;

/**
 * Class CommandList
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\System\Commandline
 * @package Scipper\ApplicationServer\System\Commandline
 */
class CommandList {

    /**
     * @var
     */
    protected $commands;


    public function __construct() {
        $this->commands = array();
    }

}