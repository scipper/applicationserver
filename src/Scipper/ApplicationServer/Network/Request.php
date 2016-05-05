<?php

namespace Scipper\ApplicationServer\Network;

/**
 * Class Request
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\Network
 * @package Scipper\ApplicationServer\Network
 */
class Request {

    /**
     * @var string
     */
    protected $method;

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
        $this->uri = "";
        $this->parameters = array();
        $this->headers = array();
    }

    /**
     * @param $header
     */
    public function withHeaderString($header) {
        // explode the string into lines.
        $lines = explode( "\n", $header );

        // extract the method and uri
        list( $method, $uri ) = explode( ' ', array_shift( $lines ) );

        $headers = [];

        foreach( $lines as $line )
        {
            // clean the line
            $line = trim( $line );

            if ( strpos( $line, ': ' ) !== false )
            {
                list( $key, $value ) = explode( ': ', $line );
                $headers[$key] = $value;
            }
        }

        $this->method = strtoupper($method);
        $this->headers = $headers;

        // split uri and parameters string
        @list($this->uri, $params) = explode('?', $uri);

        // parse the parmeters
        parse_str($params, $this->parameters);
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
    public function getUri() {
        return $this->uri;
    }

    /**
     * @param string $uri
     */
    public function setUri($uri) {
        $this->uri = $uri;
    }



}