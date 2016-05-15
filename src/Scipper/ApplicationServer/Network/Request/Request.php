<?php

namespace Scipper\ApplicationServer\Network\Request;

/**
 * Class Request
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\Network\Request
 * @package Scipper\ApplicationServer\Network\Request
 */
class Request {

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $protocol;

    /**
     * @var string
     */
    protected $uri;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var array
     */
    protected $headers;


    /**
     * Request constructor.
     */
    public function __construct() {
        $this->method = "";
        $this->protocol = "";
        $this->uri = "";
        $this->parameters = array();
        $this->headers = array();
    }

    /**
     * @param $header
     */
    public function parseHeader($header) {
        $lines = explode("\n", $header);
        list($method, $uri, $protocol) = explode(' ', array_shift($lines));

        $headers = array();

        foreach($lines as $line) {
            $line = trim($line);

            if(strpos($line, ': ') === false) {
                continue;
            }

            list($key, $value) = explode(': ', $line);
            $headers[$key] = $value;
        }

        $this->method = strtoupper($method);
        $this->protocol = $protocol;
        $this->headers = $headers;

        if(strstr($uri, '?') !== false) {
            list($this->uri, $params) = explode('?', $uri);
            parse_str($params, $this->parameters);
        }
    }

    /**
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method) {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getProtocol() {
        return (string) $this->protocol;
    }

    /**
     * @param string $protocol
     */
    public function setProtocol($protocol) {
        $this->protocol = (string) $protocol;
    }

    /**
     * @return string
     */
    public function getUri() {
        return $this->uri;
    }

    /**
     * @param string $uri
     */
    public function setUri($uri) {
        $this->uri = $uri;
    }

    /**
     * @return array
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters) {
        $this->parameters = $parameters;
    }

    /**
     * @return array
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers) {
        $this->headers = $headers;
    }

}