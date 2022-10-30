# php-rest-client
A lightweight REST client based on php-curl.

# License
This project is licensed under [GNU LGPL 3.0](LICENSE.md). 

# Installation

## By Composer

```sh
composer require technicalguru/rest-client
```

## By Package Download
You can download the source code packages from [GitHub Release Page](https://github.com/technicalguru/php-rest-client/releases)

# How to use

## Creating a Request

Depending on your intent, all you need is the URL and the body if required:

```
use TgRestClient\Request;

$request = Request::get('https://www.example.com/endpoint');
$request = Request::put('https://www.example.com/endpoint', $myObject);
$request = Request::post('https://www.example.com/endpoint', $myObject);
$request = Request::delete('https://www.example.com/endpoint/123');
```

Request provides a static method for each of the HTTP methods: HEAD, GET, POST, PUT, PATCH, OPTIONS, DELETE.

## Setting Request Headers

Additional headers for the request can be set like this:

```
use TgRestClient\Headers;

// individual headers
$request
    ->setHeader(Headers::AUTHORIZATION, 'Bearer 1234567890')
    ->setHeader(Headers::COOKIE,        'XSESSION=qwefjnqlkiu117o11ndn1');
    
// set multiple headers with a (key => value) array
$request->addHeaders($headers);
```

## The Request Body

By default, `Request` assumes your body needs to be of `application\json` type. As such,
the implementation already knows how to build a JSON encoding and you can pass an object or
array for your body. `Request` will automatically stringify. Same is valid for `application/x-www-form-urlencoded`
content. However, you will need to set the `Content-Type` header accordingly:

```
use TgRestClient\Request;
use TgRestClient\Headers;

$parameters = array(
    'name1' => 'value1',
    'name2' => 'value2',
);
$request = Request::post($url, $parameters)
    ->setHeader(Headers::CONTENT_TYPE, Headers::TYPE_X_WWW_FORM_URLENCODED);
```

## Executing

Once you configured your request, you can simply execute it:

```
$response = $request->execute();
```

The execute call takes an optional argument - the number of seconds until the call times out. The default timeout is 5 seconds.
The method will return latest after this time and give you a `Response` object.

## The Response

The response object returns everything you need to know about the result of your call:

```
$httpCode    = $response->getHttpCode();
$bodyObject  = $response->getBody();
$rawBody     = $response->getRawBody();
$headerValue = $response->getHeader('Server');
```

The `getBody()` method returns objects when the server returned a JSON-encoded body. Otherwise, you will get the
raw body by this method too.

## Defining Default Request Headers

It can be very exhausting to set the same request headers for each individual request over and over again. That's why
you can define default headers:

```
use TgRestClient\Headers;

// Single header
Headers::setDefaultHeader('www.example.com', Headers::AUTHORIZATION, 'Bearer 1234567890');

// or multiple
Headers::addDefaultHeaders('www.example.com', $myDefaultHeaders);
```

The headers will be available for each request to www.example.com. However, you can override headers
for individual requests if required. Just set the header on the request object.

## Executing Requests Simultaneously

One strength of the library is the ability to perform multiple requests at the same time:

```
use TgRestClient\Client;
use TgRestClient\Request;

// Build your request objects
$request1  = Request::get($url1);
$request2  = Request::get($url2);
...

// Create the client
$client = new Client();

// Add the requests to the client and get the Response objects
$response1 = $client->addCall($request1);
$response2 = $client->addCall($request2);
...

// Now execute all together
$responses = $client->run();
```

Please notice that the `Response` objects cannot be used before you called the `run()` method on the client.

The `run()` method again can take a timeout in seconds (which defaults to 5). It also returns an array of the `Response`
objects in the order of how you added them to the client. That means, the first response belongs to the very first request 
you added, the seconds response to the second request you added, and so on.

You can also ask the `Response` object which `Request` it was executing:

```
$request1 = $responses[0]->getRequest();
$request2 = $responses[1]->getRequest();
```

# Developer Remark

The PHP Unit test will require a [gorest.co.in](http://gorest.co.in) API token. Most tests will not be
executed without this token stored in environment variable `GOREST_TOKEN`.

# Contribution
Report a bug, request an enhancement or pull request at the [GitHub Issue Tracker](https://github.com/technicalguru/php-rest-client/issues).

