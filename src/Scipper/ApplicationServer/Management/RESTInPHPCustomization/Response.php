<?php

namespace Scipper\ApplicationServer\Management\RESTInPHPCustomization;

use RIP\Components\Framework\Core\JsonResponse;

/**
 * Class Response
 *
 * @author Steffen Kowalski <scipper@myscipper.de>
 *
 * @namespace Scipper\ApplicationServer\Management\RESTInPHPCustomization
 * @package Scipper\ApplicationServer\Management\RESTInPHPCustomization
 */
class Response extends JsonResponse {

	/**
	 * @var array
	 */
	protected $header;


	/**
	 * Response constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->header = array();
	}

	/**
	 * @param string $header
	 * @param bool $replace
	 * @param int $httpResponseCode
	 */
	public function setHeader($header, $replace = true, $httpResponseCode = NULL) {
		list($headerName, $headerValue) = explode(":", $header);
		$headerValue = trim($headerValue);

		if($replace || (!$replace && !isset($this->header[$headerName]))) {
			$this->header[$headerName] = $headerName . ":" . $headerValue;
		} else {
			$this->header[$headerName] .= "\r\n" . $headerName . ":" . $headerValue;
		}
	}

	/**
	 * 
	 * {@inheritDoc}
	 * @see \RIP\Components\Framework\Interfaces\IResponse::process()
	 */
	public function process() {
		if(isset($this->jsonArray["statuscode"])) {
			$this->statuscode = $this->jsonArray["statuscode"];
		}

		array_unshift(
			$this->header,
			"HTTP/1.1 " . $this->statuscode,
			"Content-Type: application/json;charset=utf-8",
			"Content-Language: en"
		);

		if(!isset($this->jsonArray["msg"])) {
			$this->jsonArray["msg"] = "";
		}
		if(!isset($this->jsonArray["error"])) {
			$this->jsonArray["error"] = "";
		}
		$this->jsonArray["version"] = "0.1-alpha";

		array_push($this->header, "Connection: Close");

		foreach($this->header as $header) {
			print $header . "\r\n";
		}
		print "\r\n";

		print str_replace("\\/", "/", json_encode($this->jsonArray, JSON_PRETTY_PRINT)) . "\r\n";
	}
	
}

?>