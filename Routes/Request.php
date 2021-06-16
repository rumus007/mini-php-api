<?php
include_once 'IRequest.php';

class Request implements IRequest
{
    /**
     * Request class constructor
     */
    function __construct()
    {
        $this->bootstrapSelf();
    }

    /**
     *  Method that sets all keys in the global $_SERVER array as properties of the Request class
     */
    private function bootstrapSelf()
    {
        foreach ($_SERVER as $key => $value) {
            $this->{$this->toCamelCase($key)} = $value;
        }
    }

    /**
     * Camel case string
     */
    private function toCamelCase($string)
    {
        $result = strtolower($string);

        preg_match_all('/_[a-z]/', $result, $matches);

        foreach ($matches[0] as $match) {
            $c = str_replace('_', '', strtoupper($match));
            $result = str_replace($match, $c, $result);
        }

        return $result;
    }

    /**
     * Implementation of the method defined in the IRequest interface.
     */
    public function getBody()
    {
        if ($this->requestMethod === "GET") {
            return;
        }


        if ($this->requestMethod == "POST") {

            $body = array();
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }

            return $body;
        }
    }

    /**
     * Returns query strings
     */
    public function getQueryStrings()
    {
        return $this->queryStrings;
    }

    /**
     * Returns params
     */
    public function getParams()
    {
        return $this->params;
    }
}
