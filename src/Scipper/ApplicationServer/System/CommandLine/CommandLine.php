<?php

namespace Scipper\ApplicationServer\System\CommandLine;

use Scipper\ApplicationServer\PHPApplicationServer;
use Scipper\ApplicationServer\Stream\Input\Exceptions\MappingNotFoundException;
use Scipper\ApplicationServer\Stream\Input\InputManager;
use Scipper\ApplicationServer\Stream\Output\Message;
use Scipper\ApplicationServer\Stream\Output\MessageProvider;
use Scipper\ApplicationServer\System\CommandLine\Commands\Command;
use Scipper\Colorizer\Colorizer;

/**
 * Class Commandline
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\System\CommandLine
 * @package Scipper\ApplicationServer\System\CommandLine
 */
class CommandLine {

    /**
     * @var
     */
    protected $commandList;

    /**
     * @var PHPApplicationServer
     */
    protected $server;

    /**
     * @var MessageProvider
     */
    protected $messageProvider;

    /**
     * @var InputManager
     */
    protected $inputManager;

    /**
     * @var Colorizer
     */
    protected $colorizer;


    /**
     * CommandLine constructor.
     *
     * @param PHPApplicationServer $server
     * @param MessageProvider $messageProvider
     * @param InputManager $inputManager
     * @param Colorizer $colorizer
     */
    public function __construct(PHPApplicationServer $server, MessageProvider $messageProvider, InputManager $inputManager, Colorizer $colorizer) {
        $this->commandList = array();
        $this->server = $server;
        $this->messageProvider = $messageProvider;
        $this->inputManager = $inputManager;
        $this->colorizer = $colorizer;
    }

    /**
     *
     */
    public function initializeCommands() {
        $commandsDirectory = dirname(__FILE__) . DIRECTORY_SEPARATOR . "Commands";
        $handle = opendir($commandsDirectory);

        while(false !== ($command = readdir($handle))) {
            if(substr($command, 0, 1) == "." ||
                is_dir($command)) {
                continue;
            }
            $class = $commandsDirectory . DIRECTORY_SEPARATOR . $command;
            $class = str_replace(
                array(
                    getcwd() . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR,
                    DIRECTORY_SEPARATOR,
                    ".php"
                ), array(
                    "",
                    "\\",
                    ""
                ),
                $class
            );

            $instance = new $class($this->server);
            if($instance instanceof CommandInterface) {
                $this->commandList[$instance->name()] = $instance;
            }
        }

        closedir($handle);
    }

    /**
     *
     */
    public function assignToInputManager() {
        foreach($this->commandList as $name => $command) {
            $this->inputManager->addMapping($name, function() use ($command) {
               $command->execute();
            });
        }
    }

    /**
     * @param float $tpf
     */
    public function listen($tpf) {
        try {
            if($this->inputManager->listen($tpf)) {
                $this->messageProvider->addMessage((new Message($this->colorizer))->setPromtInput());
            }
        } catch(MappingNotFoundException $e) {
            $this->messageProvider->addMessage((new Message($this->colorizer))->setKeyValueCombinesMessage("Unknown command: ", $this->inputManager->getStreamData()));
            $this->messageProvider->addMessage((new Message($this->colorizer))->setPromtMessage("Available commands are: "));
            foreach($this->inputManager->listMappings() as $mapping) {
                $this->messageProvider->addMessage((new Message($this->colorizer))->setPromtMessage("  " . $mapping, Colorizer::FG_ORANGE));
            }
            $this->messageProvider->addMessage((new Message($this->colorizer))->setPromtInput());
        }
    }

}