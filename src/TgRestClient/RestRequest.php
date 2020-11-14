<?php

namespace TgRestClient;

/**
 * Describes a REST request.
 * @author ralph
 *        
 */
class RestRequest {

    public const HEAD   = 'HEAD';
    public const GET    = 'GET';
    public const POST   = 'POST';
    public const PUT    = 'PUT';
    public const DELETE = 'DELETE';
    
    protected $method;
    protected $url;
    protected $headers;
    protected $body;
    protected $timeout;
    protected $curl;
    /**
     * Constructor.
     * @param string $method - HTTP method to be executed
     * @param string $url - the URL to be called
     */
    public function __construct($method, $url) {
        $this->method  = $method;
        $this->url     = $url;
        $this->headers = array();
        $this->body    = NULL;
        $this->timeout = 5;
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
     * Returns the timeout limit of this request.
     * @return int: the timeout in seconds
     */
    public function getTimeout() {
        return $this->timeout;
    }

/**
     * Sets a header for the request.
     * @param string $name - name of header
     * @param string value - value of header;
     */
    public function setHeader($name, $value) {
        $this->headers[$name] = $value;
    }
    
    /**
     * Adds all given headers to this request.
     * @param array $headers - headers as NAME => VALUE array
     */
    public function addHeaders($headers) {
        if (is_array($headers)) {
            foreach ($headers AS $name => $value) {
                $this->setHeader($name, $value);
            }
        }
    }
    
    /**
     * Sets the request body.
     * @param mixed $body - the body. Can be a string, an object or an array.
     */
    public function setBody($body) {
        $this->body = $body;
    }
    
    /**
     * Sets the timeout for this request.
     * @param int $timeout - number of seconds when request shall be aborted
     */
    public function setTimeout($timeout) {
        $this->timeout = $timeout;
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
        
        if (($this->method == self::PUT) || ($this->method == self::POST)) {
            $body = $this->getStringifiedBody();
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
            $this->setHeader(Headers::CONTENT_LENGTH, strlen($body));
        }
        
        $headers = array();
        foreach ($this->headers AS $name => $value) {
            $headers[] = $name.': '.$value;
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER,    $headers);        
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
}

