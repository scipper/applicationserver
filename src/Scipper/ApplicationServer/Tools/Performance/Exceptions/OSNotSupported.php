<?php

namespace Scipper\ApplicationServer\Tools\Performance\Exceptions;

use Scipper\ApplicationServer\System\Operation\ExceptionHandling\ExceptionRegister;

/**
 * Class OSNotSupported
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\Tools\Performance\Exceptions
 * @package Scipper\ApplicationServer\Tools\Performance\Exceptions
 */
class OSNotSupported extends \Exception {

    /**
     * OSNotSupported constructor.
     *
     * @param string $message
     * @param int $code
     * @param \Exception|NULL $previous
     */
    public function __construct($message = "", $code = 0, \Exception $previous = null) {
        parent::__construct(
            empty($message) ?
                "Your Operating System is not supported by this function. " :
                $message,
            ExceptionRegister::SYSTEM_OS_NOT_SUPPORTED,
            $previous
        );
    }
    
}