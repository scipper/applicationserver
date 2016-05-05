<?php

namespace Scipper\ApplicationServer\Management\RESTInPHPCustomization;

use RIP\Components\DependencyInjection\Exceptions\ClassNotFound;
use RIP\Components\DependencyInjection\Exceptions\DeclarationNotFound;
use RIP\Components\DependencyInjection\Exceptions\WrongArguments;
use RIP\Components\DependencyInjection\Interfaces\IInjector;
use RIP\Components\Develop\Timing\Stopwatch;
use RIP\Components\Framework\Core\Application;
use RIP\Components\Framework\Core\JsonResponse;
use RIP\Components\Framework\Core\Kernel;
use RIP\Components\Framework\Core\Request;
use RIP\Components\Framework\Core\Router;
use RIP\Components\Framework\Core\Server;
use RIP\Components\Framework\Interfaces\IApplication;
use RIP\Components\Framework\Interfaces\IConfiguration;
use RIP\Components\Framework\Interfaces\IKernel;
use RIP\Components\Framework\Interfaces\ILogger;
use RIP\Components\Framework\Interfaces\IResponse;

/**
 * Class RESTInPHP
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\Management\RESTInPHPCustomization
 * @package Scipper\ApplicationServer\Management\RESTInPHPCustomization
 */
final class RESTInPHP {

    /**
     *
     * @var IInjector
     */
    protected $injector;

    /**
     * @var IConfiguration
     */
    protected $applicationConfig;

    /**
     * @var boolean
     */
    protected $devmode;

    /**
     * @var ILogger
     */
    protected $logger;

    /**
     * @var IResponse
     */
    protected $response;

    /**
     * @var Stopwatch
     */
    protected $stopwatch;

    /**
     * @var IApplication
     */
    protected $application;


    /**
     * RESTInPHP constructor.
     *
     * @param IInjector $injector
     * @param IConfiguration $applicationConfig
     * @param ILogger $logger
     */
    public function __construct(IInjector $injector, IConfiguration $applicationConfig, ILogger $logger) {
        set_error_handler(array($this, "RIPErrorHandler"));

        $this->injector = $injector;
        $this->applicationConfig = $applicationConfig;

        $this->devmode = $applicationConfig->get("devmode") == true;

        $this->logger = $logger;
        $this->response = NULL;
        $this->stopwatch = NULL;
        $this->application = NULL;
    }

    /**
     *
     */
    public function boot() {
        try {
            if($this->devmode) {

                $this->stopwatch = $this->injector->injectClass("develop.stopwatch");
                $this->stopwatch->start();

            }
            /**
             * @var Application $application
             */
            $this->application = $this->injector->injectClass("framework.application");

            $this->application->init($this->applicationConfig);
        } catch(DeclarationNotFound $e) {
            $this->application = $this->handleError($e);
        } catch(ClassNotFound $e) {
            $this->application = $this->handleError($e);
        } catch(WrongArguments $e) {
            $this->application = $this->handleError($e);
        } catch(\Exception $e) {
            $this->application = $this->handleError($e);
        }
    }

    /**
     *
     */
    public function processRequest($uri, $method) {

        $router = $this->injector->getStored("framework.router");
        $kernel = $this->injector->getStored("framework.kernel");

        $router->detectCurrentRoute($uri, $method);

        $kernel->getRequest()->assignExpressionValues($router);

        $this->application->determineController();
        $this->application->determineAction();
        $this->application->executeAction();
    }

    /**
     *
     */
    public function prepareResponse() {
        $this->response = $this->application->returnResponse();

        if($this->applicationConfig->get("accept-control-allow-origin") != NULL) {
            $accepts = explode(",", $this->applicationConfig->get("accept-control-allow-origin"));
            foreach($accepts as $accept) {
                $this->response->setHeader("Access-Control-Allow-Origin: " . $accept, false);
            }
        }
        $this->response->process();
    }

    /**
     *
     */
    public function shutdown() {

    }

    /**
     * @return \RIP\Components\Framework\Core\Kernel
     */
    protected function getDefaultKernel() {
        $server = new Server();
        $request = new Request();
        $response = new JsonResponse();

        return new Kernel($server, $request, $response);
    }

    /**
     * @param \RIP\Components\Framework\Interfaces\IKernel|NULL $kernel
     *
     * @return \RIP\Components\Framework\Core\Application
     */
    protected function getDefaultApplication(IKernel $kernel = NULL) {
        if($kernel === NULL) {
            return new Application($this->getDefaultKernel(), $this->injector, new Router(""));
        }

        return new Application($kernel, $this->injector, new Router(""));
    }

    /**
     * @param \Exception $e
     *
     * @return \RIP\Components\Framework\Core\Application
     */
    protected function handleError(\Exception $e) {
        $kernel = $this->getDefaultKernel();
        $application = $this->getDefaultApplication($kernel);

        $kernel->getResponse()->addValue("error", $e->getMessage());
        $this->logger->logError($e->getMessage());

        return $application;
    }

    /**
     * @param integer $errno
     * @param string $errstr
     *
     * @return bool
     * @throws \Exception
     */
    public function RIPErrorHandler($errno, $errstr) {
        if(E_RECOVERABLE_ERROR === $errno) {
            throw new \Exception($errstr, $errno);
        }

        return false;
    }

}