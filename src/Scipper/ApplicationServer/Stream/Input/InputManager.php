<?php

namespace Scipper\ApplicationServer\Stream\Input;

use Scipper\ApplicationServer\Stream\Input\Exceptions\MappingNotFoundException;

/**
 * Class InputManager
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\Stream\Input
 * @package Scipper\ApplicationServer\Stream\Input
 */
class InputManager {

    /**
     *
     * @var array[\Closure]
     */
    protected $mappings;

    /**
     *
     * @var InputEventListenerInterface
     */
    protected $inputEventListener;


    /**
     * InputManager constructor.
     *
     * @param InputEventListenerInterface $eventListener
     */
    public function __construct(InputEventListenerInterface $eventListener) {
        $this->mappings = array();
        $this->inputEventListener = $eventListener;
    }
    /**
     *
     * @param string $key
     * @param \Closure $action
     */
    public function addMapping($key, \Closure $action) {
        $this->mappings[(string) $key] = $action;
    }

    /**
     *
     * @param string $key
     */
    public function removeMapping($key) {
        if($this->mappingExists($key)) {
            unset($this->mappings[(string) $key]);
        }
    }

    /**
     *
     * @param string $key
     * @return boolean
     */
    protected function mappingExists($key) {
        return isset($this->mappings[(string) $key]);
    }

    /**
     *
     * @param string $key
     * @return \Closure
     */
    protected function getMapping($key) {
        if($this->mappingExists($key)) {
            return $this->mappings[(string) $key];
        }
        return NULL;
    }

    /**
     * @return array
     */
    public function listMappings() {
        return array_keys($this->mappings);
    }

    /**
     * @param float $tpf
     *
     * @return bool
     * @throws MappingNotFoundException
     */
    public function listen($tpf) {
        if($this->inputEventListener->listen() &&
            $this->inputEventListener->getStream() !== null) {
            if(empty($this->inputEventListener->getStream())) {
                return true;
            }

            $mapping = $this->getMapping($this->inputEventListener->getStream());
            if(is_null($mapping)) {
                throw new MappingNotFoundException($this->inputEventListener->getStream());
            }

            $this->delegateEvent($mapping, $tpf);
            return true;
        }

        return false;
    }

    /**
     * @return string|null
     */
    public function getStreamData() {
        return $this->inputEventListener->getStream();
    }

    /**
     *
     * @param \Closure|NULL $mapping
     * @param float $tpf
     */
    protected function delegateEvent($mapping, $tpf) {
        $mapping($tpf);

        $this->inputEventListener->reset();
    }

}