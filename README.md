zenith-client
=============

<br/>
This client class extends *SoapClient* and allows you to perform a request to a Zenith application. 

Installation
------------

<br/>
**Composer**
```javascript
{
    "require": {
    	"zenith/client": "1.0.*"
    }
}
```

<br/>
Examples
--------
<br/>
The next code shows how to use this class to invoke the *Acme\HelloWorld* service.

<br/>
**Simple request**
```php
require __DIR__ . '/vendor/autoload.php';

use Zenith\Client\Client;
use Zenith\SOAP\Request;

//build request
$request = new Request;
$request->setService('Acme\HelloWorld', 'sayHi');
$request->setParameter('David');

//build client
$client = new Client('application.wsdl', array('trace' => true));

if ($client->send($request)) {
    //get response
    $response = $client->getResponse();
    
    //obtain status vars
    $statusCode = $response->getStatusCode();
    $statusMessage = $response->getStatusMessage();
    
    echo "Server returned a status code $statusCode with message '$statusMessage'\n";
    
    if ($statusCode == 0) {
        //obtain result as simple string
        $result = $response->getResult();
        echo "Result: $result\n";
    }
}
else {
    //request failed
    $faultMessage = $client->getFaultMessage();
    echo "Error: $faultMessage\n";
}
```
<br/>
**Adding options**
```php
require __DIR__ . '/vendor/autoload.php';

use Zenith\Client\Client;
use Zenith\SOAP\Request;
use Zenith\SOAP\Response;

$request = new Request();
$request->setService('Acme\HelloWorld', 'sayGoodbye');
$request->setOption('lang', 'sp');

$client = new Client('application.wsdl', array('trace' => true));

if ($client->send($request)) {
    //get response
    $response = $client->getResponse();
    
    //obtain status vars
    $statusCode = $response->getStatusCode();
    $statusMessage = $response->getStatusMessage();
    
    echo "Server returned a status code $statusCode with message '$statusMessage'\n";
    
    if ($statusCode == 0) {
        $result = $response->getResult(Response::AS_STRING);
        echo "Result: $result\n";
    }
}
else {
    //request failed
    $faultMessage = $client->getFaultMessage();
    echo "Error: $faultMessage\n";
}
```
<br/>
**Parse a returned XML**
```php
require __DIR__ . '/vendor/autoload.php';

use Zenith\Client\Client;
use Zenith\SOAP\Request;
use Zenith\SOAP\Response;

$request = new Request();
$request->setService('Acme\HelloWorld', 'parseRequest');
$request->setParameter('<user><id>36233</id><name>David</name></user>');

$client = new Client('application.wsdl', array('trace' => true));

if ($client->send($request)) {
    //get response
    $response = $client->getResponse();
    
    //obtain status vars
    $statusCode = $response->getStatusCode();
    $statusMessage = $response->getStatusMessage();
    
    echo "Server returned a status code $statusCode with message '$statusMessage'\n";
    
    if ($statusCode == 0) {
        //obtain result as a SimepleXMLElement
        $result = $response->getResult(Response::AS_SIMPLEXML);
        $id = (int) $result->userid;
        $name = (string) $result->username;
        
        echo "ID: $id\n";
        echo "Name: $name\n";
    }
}
else {
    //request failed
    $faultMessage = $client->getFaultMessage();
    echo "Error: $faultMessage\n";
}
```
License
-------
<br/>
This code is licensed under the BSD 2-Clause license.