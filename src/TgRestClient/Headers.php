<?php

namespace TgRestClient;

/**
 * Standard HTTP headers.
 * @author ralph
 *        
 */
class Headers {

    public const ACCEPT         = 'Accept';
    public const AUTHORIZATION  = 'Authorization';
    public const CONTENT_LENGTH = 'Content-Length';
    public const CONTENT_TYPE   = 'Content-Type';
    public const COOKIE         = 'Cookie';
    public const USER_AGENT     = 'User-Agent';
    
    
    public const TYPE_JSON                  = 'application/json';
    public const TYPE_HAL_JSON              = 'application/hal+json';
    public const TYPE_X_WWW_FORM_URLENCODED = 'application/x-www-form-urlencoded';
    
    protected static $defaultHeaders;
    
    /**
     * Returns the instance of default headers.
     * <p>Creates the default headers if required.</p>
     * @return array the default headers.
     */
    protected static function getDefaultHeaders() {
        if (self::$defaultHeaders == NULL) {
            self::$defaultHeaders = array(
                '*' => array(
                    Headers::CONTENT_TYPE => Headers::TYPE_JSON,
                    Headers::USER_AGENT   => 'RestClient/1.0 (https://github.com/technicalguru/php-rest-client)',
                ),
            );
        }
        return self::$defaultHeaders;
    }
    
    /**
     * Adds all given headers as default for all requests.
     * @param array $headers - headers as NAME => VALUE array
     */
    public static function addDefaultHeaders($host, $headers) {
        if (is_array($headers)) {
            foreach ($headers AS $name => $value) {
                self::setDefaultHeader($name, $value);
            }
        }
    }
    
    /**
     * Sets a default header for all requests.
     * @param string $name - name of header
     * @param string value - value of header;
     */
    public static function setDefaultHeader($host, $name, $value) {
        self::getDefaultHeaders();
        self::$defaultHeaders[$host][$name] = $value;
    }
    
    /**
     * Returns the headers defined for this host.
     * @param string hostname
     * @return array with default headers.
     */
    public static function getHeaders($host) {
        $rc = array();
        $defaults = self::getDefaultHeaders();
        $rc = array_merge($rc, $defaults['*']);
        if (isset($defaults[$host])) {
            $rc = array_merge($rc, $defaults[$host]);
        }
        return $rc;
    }
}

