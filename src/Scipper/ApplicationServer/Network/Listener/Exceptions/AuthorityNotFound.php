<?php

namespace Scipper\ApplicationServer\Network\Listener\Exceptions;

use Scipper\ApplicationServer\System\Operation\ExceptionHandling\ExceptionRegister;

/**
 * Class AuthorityAlreadyInUse
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\Network\Listener\Exceptions
 * @package Scipper\ApplicationServer\Network\Listener\Exceptions
 */
class AuthorityNotFound extends \Exception {

    /**
     * AuthorityNotFound constructor.
     *
     * @param string $message
     * @param int $code
     * @param \Exception|NULL $previous
     */
    public function __construct($message = "", $code = 0, \Exception $previous = null) {
        parent::__construct(
            empty($message) ?
                "The given authority is not registered. " :
                $message,
            ExceptionRegister::NETWORK_AUTHORITY_IN_USE,
            $previous
        );
    }

}