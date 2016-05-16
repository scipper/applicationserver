<?php

namespace Scipper\ApplicationServer\Stream\Input\Exceptions;

use Scipper\ApplicationServer\System\Operation\ExceptionHandling\ExceptionRegister;

/**
 * Class MappingNotFoundException
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\Stream\Input\Exceptions
 * @package Scipper\ApplicationServer\Stream\Input\Exceptions
 */
class MappingNotFoundException extends \Exception {

    /**
     * MappingNotFoundException constructor.
     *
     * @param string $message
     * @param int $code
     * @param \Exception|NULL $previous
     */
    public function __construct($message = "", $code = 0, \Exception $previous = null) {
        parent::__construct(
            empty($message) ?
                "Mapping not found. " :
                $message,
            ExceptionRegister::SYSTEM_MAPPING_NOT_FOUND,
            $previous
        );
    }

}