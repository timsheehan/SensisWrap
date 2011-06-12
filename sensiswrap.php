<?php
/**
 * PHP5 Sensis API Wrapper
 * 
 * This class provids an API wrapper for Australias Sensis API.
 * 
 * @author Tim Sheehan <tim@limelight-digital.com.au>
 * @version 1.0
 * @package sensis
 */
class Sensis implements Sensis_Config
{
	
   /**
    * Error storage
    * @var string 
    */
    public $errors = FALSE;
	
    /**
     * SEARCH FUNCTION
     * Takes array of search parameters and returns URL.
     * 
     * key	                string	API key (required)
     * query	            string	What to search for (required)
     * location	            string	Location to search in (required)
     * page	                number	Page number to return.
     * rows	                number	Number of listings to return per page.
     * sortBy	            string	Listing sort order.
     * sensitiveCategories	boolean	Filtering potentially unsafe content.
     * categoryId	        string	Filter listings returned by category id
     * postcode	            string	Filter listings returned by postcode
     * radius	            double	Filter listings returned to those within the radius distance of the location.
     * 
     * @param mixed $params search parameters
     * @return mixed 
     */
    public function search($params)
    {
        $required_keys = array(
            'query', 'location'
        );
        if(is_array($params) && count($params) >= count($required_keys))
        {
            if($this->check_required($required_keys, $params))
            {
                $query = http_build_query($params);
                if($url = $this->build_url($query, 'search'))
                {
                    return $this->query_api($url);
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            $this->errors[] = 'Invalid search parameters, must be an array.';
            return false;
        }
    }
    
    /**
     * Get Listing By ID
     * Takes array of search parameters and returns URL.
     * 
     * key                  string  API key (required)
     * query                string  Listing ID
     * 
     * @param mixed $params search parameters
     * @return mixed 
     */
    public function get_listing($params)
    {
        $required_keys = array(
            'query'
        );
        if(is_array($params) && count($params) == count($required_keys))
        {
            if($this->check_required($required_keys, $params))
            {
                $query = http_build_query($params);
                if($url = $this->build_url($query, 'getByListingId'))
                {
                    return $this->query_api($url);
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            $this->errors[] = 'Invalid search parameters, must be an array and contain query only.';
            return false;
        }
    }
    
    /**
     * Query API
     * Sends url to Sensis to retrive JSON object
     * -- Leveraging existing Sensis PHP example for retrieving response
     * -- TODO: Change to cURL
     * 
     * @param string $url complete url for query
     * @param bool   $raw raw PHP array or JSON response
     * @return mixed 
     */
    public function query_api($url)
    {
        $response = file_get_contents($url);       
        if (!$response) {
            $this->error[] = 'Error retrieving data.';
            return false;
        }
        else
        {
            $result = json_decode($response, true);
            $code = $result['code'];
            if($code == 200)
            {
                return $result; 
            }
            else if($code == 206)
            {
                $this->errors[] = "Note: " . $result['message'] . "\n";
                return $result;
            }
            else {
                $this->errors[] = "API returned error: " . 
                $result['message'] . ", code: " . $result['code'];
                return false;
            }
        }
    }
	
    /**
     * Build the URL
     * Combines query and interface constants to create full URL.
     * 
     * @param mixed $query search query array
     * @return mixed 
     */
    private function build_url($query, $type)
    {
        if($query)
        {
            $url_parts = array(
                self::api_url,
                self::environment,
                $type,
                $query
            );
            return implode('/', $url_parts);
        }
        else
        {
            $this->errors[] = 'Invalid query array.';
            return false;
        }
    }
	
    /**
     * Check for required keys
     * 
     * @param mixed $keys required keys
     * @param mixed $params given keys
     * @return bool
     */
    private function check_required($required_keys, $params)
    {
        foreach($required_keys as $key)
        {
            if(!array_key_exists($key, $params) || $params[$key] == '')
            {
                $this->errors[] = 'Required parameter missing - '.$key;
                return false;
            }
            else
            {
                return true;
            }
        }
    }
}