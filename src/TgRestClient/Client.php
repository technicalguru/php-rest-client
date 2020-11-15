<?php

namespace TgRestClient;

/**
 * A lightweight php-curl-based REST client that allows simultaneous requests with a timeout.
 * @author ralph
 *        
 */
class Client {

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
     * Registers a request.
     * @param Request $request - the request to be executed
     * @return Response the response object that will hold the response after execution
     */
    public function &addCall(Request $request) {
        $rc = new Response($request);
        $this->requests[]  = $request;
        $this->responses[] = $rc;
        return $rc;
    }

    /**
     * Runs the registered requests with given timeout.
     * @param int $timeout - number of seconds when the requests shall be aborted
     * @return array of Response objects
     */
    public function run($timeout = 5) {
        if (count($this->requests) > 0) {
            $mcurl = curl_multi_init();

            foreach ($this->requests as $request) {
                $curl = $request->getCurl();
                curl_setopt($curl, CURLOPT_HEADER, 1);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_multi_add_handle($mcurl, $curl);
            }

            // Execute calls
            $running_handles = 0;
            do {
                curl_multi_exec($mcurl, $running_handles);
                curl_multi_select($mcurl);
            } while ($running_handles > 0);

            // Retrieve the results and close the handles
            foreach ($this->responses as $response) {
                $response->setCurlResult();
                
                // close current handler
                curl_multi_remove_handle($mcurl, $response->getRequest()->getCurl());
            }
            curl_multi_close($mcurl);
        }

        // return results
        return $this->responses;
    }

    /**
     * Returns the number of registered requests.
     * @return int number of requests.
     */
    public function getRequestCount() {
        return count($this->requests);
    }

    /**
     * Returns the list of registered requests.
     * @return array list of requests
     */
    public function getRequests() {
        return $this->requests;
    }
    
    /**
     * Returns the list of responses for registered requests.
     * @param array list of responses.
     */
    public function getResponses() {
        return $this->responses;
    }
}

