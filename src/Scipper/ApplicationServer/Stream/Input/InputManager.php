<?php

namespace Scipper\ApplicationServer\Stream\Input;

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
     * @param KeyMapperInterface $key
     * @param \Closure $action
     */
    public function addMapping(KeyMapperInterface $key, \Closure $action) {
        $this->mappings[$key->getKey()] = $action;
    }

    /**
     *
     * @param KeyMapperInterface $key
     */
    public function removeMapping(KeyMapperInterface $key) {
        if($this->mappingExists($key)) {
            unset($this->mappings[$key->getKey()]);
        }
    }

    /**
     *
     * @param KeyMapperInterface $key
     * @return boolean
     */
    private function mappingExists(KeyMapperInterface $key) {
        return array_key_exists($key->getKey(), $this->mappings);
    }

    /**
     *
     * @param KeyMapperInterface $key
     * @return \Closure
     */
    private function getMapping(KeyMapperInterface $key) {
        if($this->mappingExists($key)) {
            return $this->mappings[$key->getKey()];
        }
        return NULL;
    }

    /**
     *
     * @param float $tpf
     */
    public function listen($tpf) {
        if($this->inputEventListener->listen() && $this->inputEventListener->getKey() !== null) {
            $this->delegateEvent($this->getMapping($this->inputEventListener->getKey()), $tpf);
        }
    }

    /**
     *
     * @param \Closure|NULL $mapping
     * @param float $tpf
     */
    private function delegateEvent($mapping, $tpf) {
        if(!is_null($mapping)) {
            $mapping($tpf);
        }

        $this->inputEventListener->reset();
    }

}