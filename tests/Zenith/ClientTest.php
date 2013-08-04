<?php
/**
 * Tests client implementation against a working Zenith application
 * Author: Emmanuel Antico
 * Note: This test requires a working Zenith application to work properly.
 * A default WSDL is supplied with a test service endpoint (see wsdl/client_test.wsdl).
 * Remeber to modify application URI before running this test.
 */
use Zenith\Client\Client;
use Zenith\SOAP\Request;
use Zenith\SOAP\Response;

class ClientTest extends \PHPUnit_Framework_TestCase {
	protected $client;
	
	public function setUp() {
		$this->client = new Client(__DIR__ . '/wsdl/client_test.wsdl', array('trace' => true));
	}
	
	/**
	 * Request tests
	 */
	
	public function testRequestHello() {
		$request = new Request();
		$request->setService('Acme/HelloWorld', 'hello');
	
		$success = $this->client->send($request);
		$this->assertTrue($success);
		
		$request = $this->client->__getLastRequest();
		$assert = file_get_contents(__DIR__ . '/assert/request1.xml');
		$this->assertEquals($assert, $request);
	}
	
	public function testRequestHelloOption() {
		$request = new Request();
		$request->setService('Acme/HelloWorld', 'hello');
		$request->setOption('test', 'value');
		
		$success = $this->client->send($request);
		$this->assertTrue($success);
	
		$request = $this->client->__getLastRequest();
		$assert = file_get_contents(__DIR__ . '/assert/request2.xml');
		$this->assertEquals($assert, $request);
	}
	
	public function testRequestHelloTwoOptions() {
		$request = new Request();
		$request->setService('Acme/HelloWorld', 'hello');
		$request->setOption('test', 'value');
		$request->setOption('another', 'value2');
	
		$success = $this->client->send($request);
		$this->assertTrue($success);
	
		$request = $this->client->__getLastRequest();
		$assert = file_get_contents(__DIR__ . '/assert/request3.xml');
		$this->assertEquals($assert, $request);
	}
	
	public function testRequestHelloParameter() {
		$request = new Request();
		$request->setService('Acme/HelloWorld', 'hello');
		$request->setOption('test', 'value');
		$request->setOption('another', 'value2');
		$request->setParameter('<to>World</to>');
	
		$success = $this->client->send($request);
		$this->assertTrue($success);
	
		$request = $this->client->__getLastRequest();
		$assert = file_get_contents(__DIR__ . '/assert/request4.xml');
		$this->assertEquals($assert, $request);
	}
	
	/**
	 * XML response tests
	 */
	
	public function testHello() {
		$request = new Request();
		$request->setService('Acme/HelloWorld', 'hello');
		
		$success = $this->client->send($request);
		$this->assertTrue($success);
		
		$response = $this->client->getResponse();
		$code = $response->getStatusCode();
		$this->assertEquals(0, $code);
		$message = $response->getStatusMessage();
		$this->assertEquals("Ok", $message);
		
		$result = $response->getResult(Response::AS_STRING);
		$this->assertEquals('Hello World :)', $result);
	}
	
	public function testGoodbye() {
		$request = new Request();
		$request->setService('Acme/HelloWorld', 'sayGoodbye');
		
		$success = $this->client->send($request);
		$this->assertTrue($success);
		
		$response = $this->client->getResponse();
		$code = $response->getStatusCode();
		$this->assertEquals(0, $code);
		$message = $response->getStatusMessage();
		$this->assertEquals("Ok", $message);
		
		$result = $response->getResult(Response::AS_STRING);
		$this->assertEquals("<message>Goodbye World!!!</message><destination>Earth</destination>", $result);
	}
	
	public function testGoodbye2() {
		$request = new Request();
		$request->setService('Acme/HelloWorld', 'sayGoodbye');
		$request->setOption('lang', 'sp');
		
		$success = $this->client->send($request);
		$this->assertTrue($success);
	
		$response = $this->client->getResponse();
		$code = $response->getStatusCode();
		$this->assertEquals(0, $code);
		$message = $response->getStatusMessage();
		$this->assertEquals("Ok", $message);
	
		$result = $response->getResult(Response::AS_STRING);
		$this->assertEquals("<message>Adios Mundo!!!</message><destination>Tierra</destination>", $result);
	}
	
	public function testHi() {
		$request = new Request();
		$request->setService('Acme/HelloWorld', 'sayHi');
		$request->setParameter('David');
		
		$success = $this->client->send($request);
		$this->assertTrue($success);
		
		$response = $this->client->getResponse();
		$code = $response->getStatusCode();
		$this->assertEquals(0, $code);
		$message = $response->getStatusMessage();
		$this->assertEquals("Ok", $message);
		
		$result = $response->getResult(Response::AS_STRING);
		$this->assertEquals("Hello David!!!", $result);
	}
	
	/**
	 * Unwrapped XML response tests
	 */
	
	public function testExpose() {
		$request = new Request();
		$request->setService('Acme/HelloWorld', 'expose');
		
		$success = $this->client->send($request);
		$this->assertTrue($success);
		
		$response = $this->client->getResponse();
		$statusCode = $response->getStatusCode();
		$this->assertEquals(0, $statusCode);
		$statusMessage = $response->getStatusMessage();
		$this->assertEquals("Ok", $statusMessage);
		$result = $response->getResult(Response::AS_STRING);
		$this->assertEquals("<class>Acme\HelloWorld</class><methods><method>hello</method><method>sayHi</method><method>sayGoodbye</method><method>expose</method><method>parseRequest</method><method>throw_fault</method><method>throw_exception</method><method>throw_service_exception</method></methods>", $result);
	}
	
	/**
	 * @expectedException RuntimeException
	 */
	public function testExpose2() {
		$request = new Request();
		$request->setService('Acme/HelloWorld', 'expose');
	
		$success = $this->client->send($request);
		$this->assertTrue($success);
	
		$response = $this->client->getResponse();
		$statusCode = $response->getStatusCode();
		$this->assertEquals(0, $statusCode);
		$statusMessage = $response->getStatusMessage();
		$this->assertEquals("Ok", $statusMessage);
		libxml_use_internal_errors(true);
		//this will trigger an exception
		$result = $response->getResult(Response::AS_SIMPLEXML);
	}
	
	/**
	 * @expectedException RuntimeException
	 */
	public function testExpose3() {
		$request = new Request();
		$request->setService('Acme/HelloWorld', 'expose');
	
		$success = $this->client->send($request);
		$this->assertTrue($success);
	
		$response = $this->client->getResponse();
		$statusCode = $response->getStatusCode();
		$this->assertEquals(0, $statusCode);
		$statusMessage = $response->getStatusMessage();
		$this->assertEquals("Ok", $statusMessage);

		//this will trigger an exception
		$result = $response->getResult(Response::AS_DOM);
	}
	
	/**
	 * Wrapped XML response test
	 */
	
	public function testParameter() {
		$request = new Request();
		$request->setService('Acme/HelloWorld', 'parseRequest');
		$request->setParameter('<user><id>536</id><name>Charles</name></user>');
		
		$success = $this->client->send($request);
		$this->assertTrue($success);
		
		$response = $this->client->getResponse();
		$statusCode = $response->getStatusCode();
		$this->assertEquals(0, $statusCode);
		$statusMessage = $response->getStatusMessage();
		$this->assertEquals("XML parsed correctly", $statusMessage);
		$result = $response->getResult(Response::AS_STRING);
		$this->assertEquals("<data><userid>536</userid><username>Charles</username></data>", $result);
	}
	
	public function testParameter2() {
		$request = new Request();
		$request->setService('Acme/HelloWorld', 'parseRequest');
		$request->setParameter('<user><id>15623</id><name>Peter</name></user>');
	
		$success = $this->client->send($request);
		$this->assertTrue($success);
	
		$response = $this->client->getResponse();
		$statusCode = $response->getStatusCode();
		$this->assertEquals(0, $statusCode);
		$statusMessage = $response->getStatusMessage();
		$this->assertEquals("XML parsed correctly", $statusMessage);
		$result = $response->getResult(Response::AS_DOM);
		$id = $result->getElementsByTagName('userid')->item(0);
		$this->assertEquals('15623', $id->nodeValue);
		$name = $result->getElementsByTagName('username')->item(0);
		$this->assertEquals('Peter', $name->nodeValue);
	}
	
	public function testParameter3() {
		$request = new Request();
		$request->setService('Acme/HelloWorld', 'parseRequest');
		$request->setParameter('<user><id>74426</id><name>Jeff</name></user>');
	
		$success = $this->client->send($request);
		$this->assertTrue($success);
	
		$response = $this->client->getResponse();
		$statusCode = $response->getStatusCode();
		$this->assertEquals(0, $statusCode);
		$statusMessage = $response->getStatusMessage();
		$this->assertEquals("XML parsed correctly", $statusMessage);
		$result = $response->getResult(Response::AS_SIMPLEXML);
		$id = (int) $result->userid;
		$this->assertEquals(74426, $id);
		$name = (string) $result->username;
	}
	
	/**
	 * Fault/Exception tests
	 */
	
	public function testFault() {
		$this->markTestSkipped(
				'...'
		);
		
		$client = new Client(__DIR__ . '/app/storage/wsdl/clienttest.wsdl', array('trace' => true));
		$client->setService('Acme/HelloWorld', 'throw_fault');
		
		$success = $client->invoke();
		$this->assertFalse($success);
		$faultCode = $client->getFaultCode();
		$this->assertEquals("Server", $faultCode);
		$faultString = $client->getFaultString();
		$this->assertEquals("Unexpected error", $faultString);
	}
	
	public function testException() {
		$this->markTestSkipped(
				'...'
		);
		
		$client = new Client(__DIR__ . '/app/storage/wsdl/clienttest.wsdl', array('trace' => true));
		$client->setService('Acme/HelloWorld', 'throw_exception');
		
		$success = $client->invoke();
		$this->assertFalse($success);
		$faultCode = $client->getFaultCode();
		$this->assertEquals("Server", $faultCode);
		$faultString = $client->getFaultString();
		$this->assertEquals("Something bad happened...", $faultString);
	}
	
	public function testServiceException() {
		$request = new Request();
		$request->setService('Acme/HelloWorld', 'throw_service_exception');
		
		$success = $this->client->send($request);
		$this->assertTrue($success);

		$response = $this->client->getResponse();
		$statusCode = $response->getStatusCode();
		$this->assertEquals(5, $statusCode);
		$statusMessage = $response->getStatusMessage();
		$this->assertEquals("A customized error response", $statusMessage);
	}
}