<?php
namespace Zenith\Client;

use Zenith\SOAP\Response;
use Zenith\SOAP\Request;

class Client extends \SoapClient {	
	/**
	 * Returned response
	 * @var array
	 */
	protected $rawResponse;
	
	/**
	 * Obtains the response obtained from server
	 * @return array
	 */
	public function getRawResponse() {
		return $this->rawResponse;
	}

	/**
	 * Obtains the response returned from the service
	 * @throws \RuntimeException
	 * @return \Zenith\SOAP\Response
	 */
	public function getResponse() {
		if (!isset($this->rawResponse)) {
			throw new \RuntimeException("No request has been made!");
		}
		
		if (is_soap_fault($this->rawResponse)) {
			throw new \RuntimeException("Server returned a SOAP Fault!");
		}
		
		//build response and return
		$response = new Response();
		$response->setService($this->rawResponse['service']->class, $this->rawResponse['service']->method);
		$response->setStatus($this->rawResponse['status']->code, $this->rawResponse['status']->message);
		
		if (!is_null($this->rawResponse['result'])) {
			if (is_array($this->rawResponse['result']->any) && array_key_exists('text', $this->rawResponse['result']->any)) {
				$response->setResult($this->rawResponse['result']->any['text']);
			}
			else {
				$response->setResult($this->rawResponse['result']->any);
			}
		}
		else {
			$response->setResult(null);
		}
		
		//set raw response
		$response->setRawResult($this->rawResponse['result']);
		
		return $response;
	}
	
	/**
	 * Obtains the fault code from the response
	 * @throws \RuntimeException
	 */
	public function getFaultCode() {
		if (!isset($this->rawResponse)) {
			throw new \RuntimeException("No request has been made!");
		}
		
		return $this->rawResponse->faultcode;
	}
	
	/**
	 * Obtains the fault string from the response
	 * @throws \RuntimeException
	 */
	public function getFaultString() {
		if (!isset($this->rawResponse)) {
			throw new \RuntimeException("No request has been made!");
		}
		
		return $this->rawResponse->faultstring;
	}
	
	/**
	 * Sends a request to a Zenith application
	 * @return boolean If the request was successful
	 */
	public function send(Request $request) {
		//build service section
		$service = $request->getService();
		
		//buid options section
		$configuration = new \stdClass();
		
		if (count($request->getConfiguration()) != 0) {
			if (count($request->getConfiguration()) == 1) {
				$conf = $request->getConfiguration();
				$configuration->option = new \stdClass();
				$configuration->option->name = key($conf);
				$configuration->option->value = current($conf);
			}
			else {
				$conf = $request->getConfiguration();
				$configuration->option = array();
				
				foreach ($conf as $k => $v) {
					$option = new \stdClass();
					$option->name = $k;
					$option->value = $v;
					$configuration->option[] = $option;
				}
			}
		}
		
		//build parameter section
		$parameter = new \stdClass();
		$parameter->any = $request->getParameter();
		
		//invoke server
		$this->rawResponse = $this->__soapCall('execute', array('service' => $service,
																'configuration' => $configuration,
																'parameter' => $parameter));
		
		//validate response
		return !is_soap_fault($this->rawResponse);
	}
}