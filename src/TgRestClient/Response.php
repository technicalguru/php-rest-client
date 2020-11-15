<?php

namespace TgRestClient;

use TgUtils\URL;

/**
 * Holds the response of a request.
 * @author ralph
 *        
 */
class Response {

    protected $request;
    protected $error;
    protected $url;
    protected $info;
    protected $httpCode;
    protected $body;
    protected $headers;
    
    /**
     * Constructor.
     * @param Request $request - the request this reponse belongs to.
     */
    public function __construct(Request $request) {
        $this->request  = $request;
        $this->error    = NULL;
        $this->url      = NULL;
        $this->info     = NULL;
        $this->httpCode = -1;
        $this->body     = NULL;
        $this->headers  = NULL;
    }
    
    /**
     * Returns the request.
     * @return Request the request that this response belongs to
     */
    public function getRequest() {
        return $this->request;
    }
    
    /**
     * Returns the error.
     * @return string: the error message. NULL if not requested yet, empty string if no error occurred.
     */
    public function getError() {
        return $this->error;
    }
    
    /**
     * Returns the effective URL that was requested (can differ from request URL).
     * @return URL: the effective URL object. NULL if not requested yet.
     */
    public function getUrl() {
        return $this->url;
    }
    
    /**
     * Returns the information about the response.
     * @return array: associative array of response properties. NULL if not requested yet, empty array if an error occurred.
     */
    public function getInfo() {
        return $this->info;
    }
    
    /**
     * Returns the content type of the response.
     * @param string the content type or NULL
     */
    public function getContentType() {
        if (isset($this->info['content_type'])) {
            return $this->info['content_type'];
        }
    }
    
    /**
     * Returns the HTTP response code.
     * @return int: the HTTP response code. -1 if not requested yet, 0 if an error occurred.
     */
    public function getHttpCode() {
        return $this->httpCode;
    }

    /**
     * Returns the headers from the response.
     * @return array: the response headers.
     */
    public function getHeaders() {
        return $this->headers;
    }
    
    /**
     * Returns the response header with the given name.
     * @param string $name - name of header, NULL when the first line shall be returned (status line).
     * @return string the header value or the status header
     */
    public function getHeader($name = NULL) {
        if ($name == NULL) {
            return $this->headers[0];
        }
        foreach ($this->headers AS $header) {
            if (strpos(strtolower($header), strtolower($name).':') === 0) {
                return trim(substr($header, strlen($name)+1));
            }
        }
        return NULL;
    }
    
    /**
     * Returns the raw response body (as string).
     * @return string: the response body. NULL if not requested yet.
     */
    public function getRawBody() {
        return $this->body;
    }
    
    /**
     * Returns the response body decoded (if possible).
     * <p>The response content type will be asked to decide how to decode.</p>
     * @return mixed: the response body as object, array or string. NULL if not requested yet.
     */
    public function getBody() {
        if (strpos($this->getContentType(), Headers::TYPE_JSON) === 0) {
            return $this->getJsonDecodedBody();
        }
        return $this->body;
    }
    
    /**
     * Returns the response body as JSON decoded object.
     * @return object: the response body. NULL if not requested yet.
     */
    public function getJsonDecodedBody() {
        return $this->body != NULL ? json_decode(trim($this->body)) : NULL;
    }
    
    /**
     * Loads the response information from the curl handle in the request.
     */
    public function setCurlResult() {
        $curl = $this->request->getCurl();
        
        $this->error    = curl_error($curl);
        $this->url      = new URL(curl_getinfo($curl, CURLINFO_EFFECTIVE_URL));
        if (empty($this->error)) {
            $this->httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $this->info     = curl_getinfo($curl);
            $response       = curl_multi_getcontent($curl);
            
            $headerSize     = $this->info['header_size'];
            $this->headers  = explode("\n", str_replace("\r", '', substr($response, 0, $headerSize)));
            $this->body     = substr($response, $headerSize);
        } else {
            $this->httpCode = 0;
            $this->info     = array();
            $this->body     = '';
        }
    }
}

