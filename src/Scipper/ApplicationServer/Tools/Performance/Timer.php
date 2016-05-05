<?php

namespace Scipper\ApplicationServer\Tools\Performance;

/**
 * Class Timer
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\Tools\Performance
 * @package Scipper\ApplicationServer\Tools\Performance
 */
class Timer {

    /**
     *
     * @var float
     */
    private $currentTime;

    /**
     *
     * @var float
     */
    private $lastTime;

    /**
     *
     * @var float
     */
    private $elapsed;


    /**
     *
     */
    public function __construct() {
        $this->currentTime = 0.0;
        $this->lastTime = $this->microtime();
        $this->elapsed = 0.0;
    }

    /**
     *
     * @return float
     */
    public function microtime() {
        return microtime(true);
    }

    /**
     *
     */
    public function update() {
        $this->currentTime = $this->microtime();
        $this->elapsed = $this->currentTime - $this->lastTime;
        $this->lastTime = $this->currentTime;
    }
    /**
     *
     * @return float
     */
    public function getElapsed() {
        return $this->elapsed;
    }

    /**
     *
     */
    public function adjust() {
        $frameTicks = $this->getElapsed();
        if($frameTicks < 1) {
            usleep(1000 - $frameTicks);
        }
    }

}