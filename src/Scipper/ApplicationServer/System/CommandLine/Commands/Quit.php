<?php

namespace Scipper\ApplicationServer\System\CommandLine\Commands;

use Scipper\ApplicationServer\PHPApplicationServer;
use Scipper\ApplicationServer\System\CommandLine\CommandInterface;

/**
 * Class Quit
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\System\CommandLine\Commands
 * @package Scipper\ApplicationServer\System\CommandLine\Commands
 */
class Quit implements CommandInterface {

    /**
     * @var PHPApplicationServer
     */
    protected $server;


    /**
     * Quit constructor.
     *
     * @param PHPApplicationServer $server
     */
    public function __construct(PHPApplicationServer $server) {
        $this->server = $server;
    }

    /**
     * @return string
     */
    public function name() {
        return "quit";
    }

    /**
     *
     */
    public function execute() {
        $this->server->stop();
    }

}