<?php

namespace Scipper\ApplicationServer\Stream\Output;

use Scipper\Colorizer\Colorizer;

/**
 * Class Message
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\Stream\Output
 * @package Scipper\ApplicationServer\Stream\Output
 */
class Message {

    /**
     * @var Colorizer
     */
    protected $colorizer;

    /**
     * @var string
     */
    protected $message;


    /**
     * Message constructor.
     *
     * @param Colorizer $colorizer
     * @param string $message
     */
    public function __construct(Colorizer $colorizer, $message = "") {
        $this->colorizer = $colorizer;
        $this->setMessage($message);
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->getMessage();
    }

    /**
     * @param $message
     * @param string $customColor
     * @param bool $linebreak
     */
    public function setPromtMessage($message, $customColor = Colorizer::FG_CYAN, $linebreak = true) {
        $this->message = $this->colorizer->colorize("| ", Colorizer::FG_CYAN);
        $this->message .= $this->colorizer->colorize($message, $customColor);
        if($linebreak) {
            $this->message .= PHP_EOL;
        }
    }

    /**
     * @param string $message
     * @param string $customColor
     * @param bool $linebreak
     */
    public function setMessage($message, $customColor = Colorizer::FG_CYAN, $linebreak = true) {
        $this->message .= $this->colorizer->colorize($message, $customColor);
        if($linebreak) {
            $this->message .= PHP_EOL;
        }
    }

    /**
     * @param $key
     * @param $value
     */
    public function setKeyValueCombinesMessage($key, $value) {
        $this->setPromtMessage($key, Colorizer::FG_CYAN, false);
        $this->setMessage($value, Colorizer::FG_ORANGE);
    }

    /**
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

}