<?php

namespace Scipper\ApplicationServer\Management;

use RIP\Components\DependencyInjection\ConfigurationLoader;
use RIP\Components\DependencyInjection\Interfaces\IInjector;
use RIP\Components\Framework\Core\IniConfig;
use RIP\Components\Framework\Core\JsonConfig;
use Scipper\ApplicationServer\Management\RESTInPHPCustomization\RESTInPHP;

/**
 * Class ManagementLoader
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\Management
 * @package Scipper\ApplicationServer\Management
 */
class ManagementLoader {

    /**
     * @var string
     */
    protected $ds;

    /**
     * @var string
     */
    protected $documentRoot;

    /**
     * @var string
     */
    protected $configFolder;


    /**
     * ManagementLoader constructor.
     */
    public function __construct() {
        $this->ds = DIRECTORY_SEPARATOR;
        $this->documentRoot = getcwd() . $this->ds;
        $this->configFolder = $this->documentRoot . "app" . $this->ds . "management" . $this->ds . "config" . $this->ds;
    }

    /**
     * @return RESTInPHP
     */
    public function getManagementServerInstance() {
        //******
        //Loading IInjector implementaion
        $injectionConfigLoader = new ConfigurationLoader(".injection.json");
        $injectionConfig = $injectionConfigLoader->loadConfigurations(new JsonConfig(), $this->configFolder . "injection/", true);

        $injectorImpl = $injectionConfig->get("framework.injector");
        if(empty($injectorImpl) || !isset($injectorImpl["class"])) {
            $injectorImpl = "RIP\\Components\\DependencyInjection\\DefaultInjector";
        } else {
            $injectorImpl = $injectorImpl["class"];
        }

        /**
         * @var IInjector $injector
         */
        $injector = new $injectorImpl($injectionConfig);
        $injector->store("framework.injector", $injector);
        //******


        //******
        //Loading php.ini configurations
        $phpConfigLoader = new ConfigurationLoader(".php.ini");
        $phpConfig = $phpConfigLoader->loadConfigurations(new IniConfig(), $this->configFolder . "server/");
        foreach($phpConfig as $option => $value) {
            ini_set($option, $value);
        }
        //******


        //******
        //Loading system configurations
        $systemConfigLoader = new ConfigurationLoader(".system.ini");
        $systemConfig = $systemConfigLoader->loadConfigurations(new IniConfig(), $this->configFolder . "server/");
        foreach($systemConfig as $function => $value) {
            $function($value);
        }
        //******


        //******
        //Loading application configurations
        $applicationConfig = new IniConfig();
        $applicationConfig->loadFile($this->configFolder . "application.ini");
        //******


        //******
        //Loading ILogger implementation
        $loggerImpl = $injectionConfig->get("framework.logger");
        if(empty($loggerImpl) || !isset($loggerImpl["class"])) {
            $loggerImpl = "RIP\\Components\\Framework\\Core\\Logger";
        } else {
            $loggerImpl = $loggerImpl["class"];
        }
        $logger = new $loggerImpl();
        //******


        //******
        //Loading IRouter implementation
        $routerImpl = $injectionConfig->get("framework.router");
        if(empty($routerImpl) || !isset($routerImpl["class"])) {
            $routerImpl = "RIP\\Components\\Framework\\Core\\Router";
        } else {
            $routerImpl = $routerImpl["class"];
        }
        $router = new $routerImpl($this->configFolder . "routes.json");
        $injector->store("framework.router", $router);
        //******


        //******
        //Boot RESTInPHP
        $rip = new RESTInPHP($injector, $applicationConfig, $logger);
        //******

        return $rip;
    }

}