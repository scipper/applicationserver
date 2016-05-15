<?php

namespace Scipper\ApplicationServer\Tools\Performance;

use Scipper\ApplicationServer\Tools\Performance\Exceptions\OSNotSupported;

/**
 * Class LoadMonitor
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\Tools\Performance
 * @package Scipper\ApplicationServer\Tools\Performance
 */
class LoadMonitor {

    /**
     * @return float
     * @throws OSNotSupported
     */
    public function getServerLoad() {
        if(stristr(PHP_OS, 'win')) {
            throw new OSNotSupported();
        }
        $sysLoad = sys_getloadavg();
        $load = $sysLoad[0];

        return $load;
    }

}