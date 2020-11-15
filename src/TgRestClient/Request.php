<?php

namespace TgRestClient;

use TgUtils\URL;

/**
 * Describes a REST request.
 * @author ralph
 *        
 */
class Request {

    public const HEAD    = 'HEAD';
    public const GET     = 'GET';
    public const OPTIONS = 'OPTIONS';
    public const PATCH   = 'PATCH';
    public const POST    = 'POST';
    public const PUT     = 'PUT';
    public const DELETE  = 'DELETE';
    
    protected $method;
    protected $url;
    protected $headers;
    protected $body;
    protected $curl;
    
    /**
     * Constructor.
     * @param string $method - HTTP method to be executed
     * @param string $url - the URL to be called
     */
    public function __construct($method, $url) {
        $this->method  = $method;
        $this->url     = new URL($url);
        $this->headers = Headers::getHeaders($this->url->getHost());
        $this->body    = NULL;
        $this->curl    = NULL;
    }
    
    /**
     * Returns the method of this request.
     * @return string the method
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * Returns the URL to be requested
     * @return string the URL
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Returns all headers defined for this request.
     * @return array: the headers
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * Returns the body of this request.
     * @return mixed: the body
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * Sets a header for the request.
     * @param string $name - name of header
     * @param string value - value of header
     * @return Request this object for chaining
     */
    public function setHeader($name, $value) {
        $this->headers[$name] = $value;
        return $this;
    }
    
    /**
     * Adds all given headers to this request.
     * @param array $headers - headers as NAME => VALUE array
     * @return Request this object for chaining
     */
    public function addHeaders($headers) {
        if (is_array($headers)) {
            foreach ($headers AS $name => $value) {
                $this->setHeader($name, $value);
            }
        }
        return $this;
    }
    
    /**
     * Sets the request body.
     * @param mixed $body - the body. Can be a string, an object or an array.
     * @return Request this object for chaining
     */
    public function setBody($body) {
        $this->body = $body;
        return $this;
    }
    
    /**
     * Returns the configured curl resource handle for this request.
     * @return mixed: the curl resource handle
     */
    public function getCurl() {
        if ($this->curl == NULL) {
            $this->curl = $this->createCurl();
        }
        return $this->curl;
    }
    
    /**
     * Creates the configured curl resource handle for this request.
     * @return mixed: the curl resource handle
     */
    public function createCurl() {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->method);       
        curl_setopt($curl, CURLOPT_TIMEOUT,       $this->timeout);
        
        if (($this->method == self::PATCH) || ($this->method == self::PUT) || ($this->method == self::POST)) {
            $body = $this->getStringifiedBody();
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
            $this->setHeader(Headers::CONTENT_LENGTH, strlen($body));
        }
        
        $headers = array();
        foreach ($this->headers AS $name => $value) {
            $headers[] = $name.': '.$value;
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER,    $headers);
        
        return $curl;
    }
    
    /**
     * Strimgifies the body.
     * <p>The method tries to encode the body when it is an array or object according to the 
     *    defined content type header (JSON and URLENCODED supported).</p>
     * @return string: the stringified body
     */
    public function getStringifiedBody() {
        $body = $this->body;
        $rc   = '';
        if ($body != NULL) {
            if (is_object($body)) {
                $body = get_object_vars($body);
            }
            
            if (is_array($body)) {
                // stringify here with JSON or www-url-encoded
                $type = $this->headers[Headers::CONTENT_TYPE];
                if (($type == Headers::TYPE_JSON) || ($type == 'text/json')) {
                    $rc = json_encode($body);
                } else if ($type == Headers::TYPE_X_WWW_FORM_URLENCODED) {
                    foreach ($body AS $key => $value) {
                        $rc .= '&'.rawurlencode($key).'='.rawurlencode($value);
                    }
                    $rc = substr($rc, 1);
                } else if (is_object($this->body)) {
                    $rc = $this->body->__toString();
                }
            }
            
            if (is_string($body) || is_numeric($body)) {
                $rc = $body;
            }
        }
        
        return $rc;
    }
    
    /**
     * Execute this request as a single request.
     * @param int $timeout - when the request shall time out (in seconds, optional, default is 5)
     * @return Response the response object.
     */
    public function execute($timeout = 5) {
        $client = new Client();
        $rc = $client->addCall($this);
        $client->run($timeout);
        return $rc;
    }
    
    
    /**
     * Shorthand function for creating a HEAD request.
     * @param string $url - the URL to be called
     * @return Request the request object created
     */
    public static function head($url) {
        return new Request(Request::HEAD, $url);
    }

    /**
     * Shorthand function for creating a GET request.
     * @param string $url - the URL to be called
     * @return Request the request object created
     */
    public static function get($url) {
        return new Request(Request::GET, $url);
    }

    /**
     * Shorthand function for creating a POST request.
     * @param string $url - the URL to be called
     * @param mixed $body - the body. Can be a string, an object or an array.
     * @return Request the request object created
     */
    public static function post($url, $body) {
        $rc = new Request(Request::POST, $url);
        $rc->setBody($body);
        return $rc;
    }

    /**
     * Shorthand function for creating a PUT request.
     * @param string $url - the URL to be called
     * @param mixed $body - the body. Can be a string, an object or an array.
     * @return Request the request object created
     */
    public static function put($url, $body) {
        $rc = new Request(Request::PUT, $url);
        $rc->setBody($body);
        return $rc;
    }

    /**
     * Shorthand function for creating a PATCH request.
     * @param string $url - the URL to be called
     * @param mixed $body - the body. Can be a string, an object or an array.
     * @return Request the request object created
     */
    public static function patch($url, $body) {
        $rc = new Request(Request::PATCH, $url);
        $rc->setBody($body);
        return $rc;
    }

    /**
     * Shorthand function for creating an OPTIONS request.
     * @param string $url - the URL to be called
     * @return Request the request object created
     */
    public static function options($url) {
        return new Request(Request::OPTIONS, $url);
    }

    /**
     * Shorthand function for creating a DELETE request.
     * @param string $url - the URL to be called
     * @return Request the request object created
     */
    public static function delete($url) {
        return new Request(Request::DELETE, $url);
    }

    
}

