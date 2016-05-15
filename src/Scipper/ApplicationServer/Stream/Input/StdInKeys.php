<?php

namespace Scipper\ApplicationServer\Stream\Input;

/**
 * Class StdInKeys
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\Stream\Input
 * @package Scipper\ApplicationServer\Stream\Input
 */
class StdInKeys implements KeyMapperInterface {

    const KEY_A = "61";
    const KEY_B = "62";
    const KEY_C = "63";
    const KEY_D = "64";
    const KEY_E = "65";
    const KEY_F = "66";
    const KEY_G = "67";
    const KEY_H = "68";
    const KEY_I = "69";
    const KEY_J = "6a";
    const KEY_K = "6b";
    const KEY_L = "6c";
    const KEY_M = "6d";
    const KEY_N = "6e";
    const KEY_O = "6f";
    const KEY_P = "70";
    const KEY_Q = "71";
    const KEY_R = "72";
    const KEY_S = "73";
    const KEY_T = "74";
    const KEY_U = "75";
    const KEY_V = "76";
    const KEY_W = "77";
    const KEY_X = "78";
    const KEY_Y = "79";
    const KEY_Z = "7a";

    const KEY_SZ = "c39f";
    const KEY_QUESTIONMARK = "3f";
    const KEY_EXCLAMATIONMARK = "21";

    const KEY_0 = "30";
    const KEY_1 = "31";
    const KEY_2 = "32";
    const KEY_3 = "33";
    const KEY_4 = "34";
    const KEY_5 = "35";
    const KEY_6 = "36";
    const KEY_7 = "37";
    const KEY_8 = "38";
    const KEY_9 = "39";

    const KEY_ENTER = "0a";
    const KEY_ESC = "1b";
    const KEY_SPACE = "20";
    const KEY_BACKSPACE = "7f";
    const KEY_UP = "1b5b61";
    const KEY_DOWN = "1b5b62";
    const KEY_LEFT = "1b5b64";
    const KEY_RIGHT = "1b5b63";

    /**
     *
     * @var string
     */
    protected $key;


    /**
     *
     * @param string $key
     */
    public function __construct($key = NULL) {
        $this->key = strtolower(trim($key));
    }

    /**
     * @return string
     */
    public function getKey() {
        return $this->key;
    }

}