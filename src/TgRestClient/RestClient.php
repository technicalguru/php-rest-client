<?php

namespace TgRestClient;

/**
 * A lightweight php-curl-based REST client that allows simultaneous requests with a timeout.
 * @author ralph
 *        
 */
class RestClient {

    protected static $defaultHeaders;
    
    protected $requests;
    protected $responses;

    /**
     * Simple constructor.
     */
    public function __construct() {
        $this->requests  = array();
        $this->responses = array();
    }

    /**
     * Adds all given headers as default for all requests.
     * @param array $headers - headers as NAME => VALUE array
     */
    public static function addDefaultHeaders($headers) {
        if (is_array($headers)) {
            foreach ($headers AS $name => $value) {
                self::setHeader($name, $value);
            }
        }
    }
    
    /**
     * Sets a default header for all requests.
     * @param string $name - name of header
     * @param string value - value of header;
     */
    public static function setDefaultHeader($name, $value) {
        if (self::$defaultHeaders == NULL) {
            self::$defaultHeaders = array();
        }
        self::$defaultHeaders[$name] = $value;
    }
    
    /**
     * Adds a GET request for simultaneous execution.
     * @param string $url - the URL to be called
     * @param string $contentType - the Content-Type of the request (optional, default is 'application/json')
     * @param array  $headers - headers to be transmitted along with this request (amends/overrides default headers)
     * @return RestResponse the response object that will hold the response after execution
     */
    public function &addGet($url, $contentType = 'application/json', $headers = array()) {
        $request = new RestRequest(RestRequest::GET, $url);
        $request->addHeaders(self::$defaultHeaders);
        $request->setHeader(Headers::CONTENT_TYPE, $contentType);
        $request->addHeaders($headers);
        return $this->addCall($request);
    }

    /**
     * Shorthand function for single GET request.
     * @param string $url - the URL to be called
     * @param string $contentType - the Content-Type of the request (optional, default is 'application/json')
     * @param array  $headers - headers to be transmitted along with this request (amends/overrides default headers)
     * @param int $timeout - number of seconds when the request shall be aborted
     * @return RestResponse the response object with the result
     */
    public static function get($url, $contentType = 'application/json', $headers = array(), $timeout = 5) {
        $client = new RestClient();
        $rc = $client->addGet($url, $contentType, $headers);
        $client->run($timeout);
        return $rc;
    }

    /**
     * Adds a PUT request for simultaneous execution.
     * @param string $url - the URL to be called
     * @param mixed $body - the body. Can be a string, an object or an array.
     * @param string $contentType - the Content-Type of the request (optional, default is 'application/json')
     * @param array  $headers - headers to be transmitted along with this request (amends/overrides default headers)
     * @return RestResponse the response object that will hold the response after execution
     */
    public function &addPut($url, $body, $contentType = 'application/json', $headers = array()) {
        $request = new RestRequest(RestRequest::PUT, $url);
        $request->addHeaders(self::$defaultHeaders);
        $request->setHeader(Headers::CONTENT_TYPE, $contentType);
        $request->addHeaders($headers);
        $request->setBody($body);
        return $this->addCall($request);
    }

    /**
     * Shorthand function for single PUT request.
     * @param string $url - the URL to be called
     * @param mixed $body - the body. Can be a string, an object or an array.
     * @param string $contentType - the Content-Type of the request (optional, default is 'application/json')
     * @param array  $headers - headers to be transmitted along with this request (amends/overrides default headers)
     * @param int $timeout - number of seconds when the request shall be aborted
     * @return RestResponse the response object with the result
     */
    public static function put($url, $body, $contentType = 'application/json', $headers = array(), $timeout = 5) {
        $client = new RestClient();
        $rc = $client->addPut($url, $body, $contentType, $headers);
        $client->run($timeout);
        return $rc;
    }

    /**
     * Adds a POST request for simultaneous execution.
     * @param string $url - the URL to be called
     * @param mixed $body - the body. Can be a string, an object or an array.
     * @param string $contentType - the Content-Type of the request (optional, default is 'application/json')
     * @param array  $headers - headers to be transmitted along with this request (amends/overrides default headers)
     * @return RestResponse the response object that will hold the response after execution
     */
    public function &addPost($url, $body, $contentType = 'application/json', $headers = array()) {
        $request = new RestRequest(RestRequest::POST, $url);
        $request->addHeaders(self::$defaultHeaders);
        $request->setHeader(Headers::CONTENT_TYPE, $contentType);
        $request->addHeaders($headers);
        $request->setBody($body);
        return $this->addCall($request);
    }

    /**
     * Shorthand function for single POST request.
     * @param string $url - the URL to be called
     * @param mixed $body - the body. Can be a string, an object or an array.
     * @param string $contentType - the Content-Type of the request (optional, default is 'application/json')
     * @param array  $headers - headers to be transmitted along with this request (amends/overrides default headers)
     * @param int $timeout - number of seconds when the request shall be aborted
     * @return RestResponse the response object with the result
     */
    public static function post($url, $body, $contentType = 'application/json', $headers = array(), $timeout = 5) {
        $client = new RestClient();
        $rc = $client->addPost($url, $body, $contentType, $headers);
        $client->run($timeout);
        return $rc;
    }

    /**
     * Adds a DELETE request for simultaneous execution.
     * @param string $url - the URL to be called
     * @param string $contentType - the Content-Type of the request (optional, default is 'application/json')
     * @param array  $headers - headers to be transmitted along with this request (amends/overrides default headers)
     * @return RestResponse the response object that will hold the response after execution
     */
    public function &addDelete($url, $contentType = 'application/json', $headers = array()) {
        $request = new RestRequest(RestRequest::DELETE, $url);
        $request->addHeaders(self::$defaultHeaders);
        $request->setHeader(Headers::CONTENT_TYPE, $contentType);
        $request->addHeaders($headers);
        return $this->addCall($request);
    }

    /**
     * Shorthand function for single DELETE request.
     * @param string $url - the URL to be called
     * @param string $contentType - the Content-Type of the request (optional, default is 'application/json')
     * @param array  $headers - headers to be transmitted along with this request (amends/overrides default headers)
     * @param int $timeout - number of seconds when the request shall be aborted
     * @return RestResponse the response object with the result
     */
    public static function delete($url, $contentType = 'application/json', $headers = array(), $timeout = 5) {
        $client = new RestClient();
        $rc = $client->addDelete($url, $contentType, $headers);
        $client->run($timeout);
        return $rc;
    }

    /**
     * Adds a HEAD request for simultaneous execution.
     * @param string $url - the URL to be called
     * @param string $contentType - the Content-Type of the request (optional, default is 'application/json')
     * @param array  $headers - headers to be transmitted along with this request (amends/overrides default headers)
     * @return RestResponse the response object that will hold the response after execution
     */
    public function &addHead($url, $contentType = 'application/json', $headers = array()) {
        $request = new RestRequest(RestRequest::HEAD, $url);
        $request->addHeaders(self::$defaultHeaders);
        $request->setHeader(Headers::CONTENT_TYPE, $contentType);
        $request->addHeaders($headers);
        return $this->addCall($request);
    }

    /**
     * Shorthand function for single HEAD request.
     * @param string $url - the URL to be called
     * @param string $contentType - the Content-Type of the request (optional, default is 'application/json')
     * @param array  $headers - headers to be transmitted along with this request (amends/overrides default headers)
     * @param int $timeout - number of seconds when the request shall be aborted
     * @return RestResponse the response object with the result
     */
    public static function head($url, $contentType = 'application/json', $headers = array(), $timeout = 5) {
        $client = new RestClient();
        $rc = $client->addHead($url, $contentType, $headers);
        $client->run($timeout);
        return $rc;
    }

    /**
     * Registers a request.
     * @param RestRequest $request - the request to be executed
     * @return RestResponse the response object that will hold the response after execution
     */
    public function &addCall(RestRequest $request) {
        $rc = new RestResponse($request);
        $this->requests[]  = $request;
        $this->responses[] = $rc;
        return $rc;
    }

    /**
     * Runs the registered requests with given timeout.
     * @param int $timeout - number of seconds when the requests shall be aborted
     * @return array of RestResponse objects
     */
    public function run($timeout = 5) {
        if (count($this->requests) > 0) {
            $mcurl = curl_multi_init();

            foreach ($this->requests as $request) {
                $curl = $request->getCurl();
                curl_setopt($curl, CURLOPT_HEADER, 0);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_USERAGENT, 'RestClient/1.0');
                curl_multi_add_handle($mcurl, $curl);
            }

            $running_handles = 0;
            // Execute calls
            do {
                curl_multi_exec($mcurl, $running_handles);
                curl_multi_select($mcurl);
            } while ($running_handles > 0);

            // close the handles and get content
            foreach ($this->responses as $response) {
                $request = $response->getRequest();
                $curl    = $request->getCurl();
                
                $response->setCurlResult();

                // close current handler
                curl_multi_remove_handle($mcurl, $curl);
            }
            curl_multi_close($mcurl);
        }

        // return results
        return $this->responses;
    }

    public function getRequestCount() {
        return count($this->requests);
    }

    public function getRequests() {
        return $this->requests;
    }
    
    public function getResponses() {
        return $this->responses;
    }
}

