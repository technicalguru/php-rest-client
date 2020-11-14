<?php

namespace TgRestClient;

/**
 * Holds the response of a request.
 * @author ralph
 *        
 */
class RestResponse {

    protected $request;
    protected $error;
    protected $url;
    protected $info;
    protected $httpCode;
    protected $body;
    
    /**
     * Constructor.
     * @param RestRequest $request - the request this reponse belongs to.
     */
    public function __construct(RestRequest $request) {
        $this->request  = $request;
        $this->error    = NULL;
        $this->url      = NULL;
        $this->info     = NULL;
        $this->httpCode = -1;
        $this->body     = NULL;
    }
    
    /**
     * Returns the request.
     * @return RestRequest the request that this response belongs to
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
     * @return string: the effective URL. NULL if not requested yet.
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
     * Returns the HTTP response code.
     * @return int: the HTTP response code. -1 if not requested yet, 0 if an error occurred.
     */
    public function getHttpCode() {
        return $this->httpCode;
    }
    
    /**
     * Returns the response body.
     * @return string: the response body. NULL if not requested yet.
     */
    public function getBody() {
        return $this->body;
    }
    
    /**
     * Returns the response body as JSON decoded object.
     * @return object: the response body. NULL if not requested yet.
     */
    public function getDecodedBody() {
        return $this->body != NULL ? json_decode($this->body) : NULL;
    }
    
    /**
     * Loads the response information from the curl handle in the request.
     */
    public function setCurlResult() {
        $curl = $this->request->getCurl();
        
        $this->error    = curl_error($curl);
        $this->url      = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
        if (empty($this->error)) {
            $this->httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $this->info     = curl_getinfo($curl);
            $this->body     = curl_multi_getcontent($curl);
        } else {
            $this->httpCode = 0;
            $this->info     = array();
            $this->body     = '';
        }
    }
}

