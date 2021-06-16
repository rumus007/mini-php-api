<?php

class Router
{
    private $supportedRequestMethods = [
        'GET',
        'POST'
    ];
    private $params = [];

    /**
     * Router Constructor
     * 
     */
    function __construct(IRequest $request)
    {
        $this->request = $request;
        $this->request->params = null;
        $this->request->queryStrings = $this->mapQueryStrings($request->queryString);
    }

    /**
     * Function overloading for request type
     */
    function __call($requestMethod, $args)
    {
        list($route, $routeHandler) = $args;

        if (!in_array(strtoupper($requestMethod), $this->supportedRequestMethods)) {
            $this->invalidRequestMethodHandler();
        }

        $this->{strtolower($requestMethod)}[$this->formatRoute($route)] = $routeHandler;
    }

    /**
     * Registering all routes with no parameters
     */
    private function formatRoute($route)
    {
        $route = rtrim($route, '/');
        $path = explode('/', $route);
        $params = [];

        foreach ($path as $token) {
            if ($token[0] === '{' and $token[strlen($token) - 1] === '}') {
                $token = ltrim(rtrim($token, '}'), '{');
                array_push($params, $token);
            }
        }

        // Register all routes with and without URL parameters
        if ($params) $this->params[$route] = $params;
        else $this->params[$route] = false;

        return $route === '' ? '/' : $route;
    }

    /**
     * Map Query string
     */
    private function mapQueryStrings($string)
    {
        $final = [];

        if (!$string) return null;

        $strings = explode("&", $string);

        foreach ($strings as $v) {
            $tmp = explode("=", $v);
            $final[$tmp[0]] = $tmp[1];
        }

        return $final;
    }

    /**
     * Register routes with parameters
     */
    private function mapRequestedRoute($requested_route)
    {
        $route = rtrim($requested_route, '/');

        // Find routes with params
        $params = $this->params[$route];

        // Route without params
        if ($params === false) {
            return $route === '' ? '/' : $route;
        }

        // Route with params i.e params is null
        foreach ($this->params as $key => $value) {
            if (is_array($this->params[$key])) { // Only routes with params
                $flag = true; // Reset for each iteration
                $store_params = [];
                $store_values = [];

                $stored_path = explode('/', $key);
                $requested_path = explode('/', $route);

                for ($i = 1; $i < count($requested_path); $i++) {
                    if ($stored_path[$i][0] === '{' && $stored_path[$i][strlen($stored_path[$i]) - 1] === '}') {
                        array_push($store_params, ltrim(rtrim($stored_path[$i], '}'), '{'));
                        array_push($store_values, $requested_path[$i]);
                        continue;
                    } else if ($stored_path[$i] !== $requested_path[$i]) {
                        $flag = false;
                    }
                }

                if (($flag === true) && (count($requested_path) === count($stored_path))) {
                    // Append params to request object
                    for ($i = 0; $i < count($store_params); $i++) {
                        $this->request->params->{$store_params[$i]} = substr($store_values[$i], 0, strpos($store_values[$i], "?"));
                    }
                    return $key;
                }
            }
        }
    }

    /**
     * Method not allowred handler
     */
    private function invalidRequestMethodHandler()
    {
        header("{$this->request->serverProtocol} 405 Method Not Allowed");
    }

    /**
     * Route not found handler
     */
    private function defaultRequestHandler()
    {
        header("{$this->request->serverProtocol} 404 Not Found");
    }

    function resolve()
    {
        $requestMethodDictionary = $this->{strtolower($this->request->requestMethod)};
        $formattedRoute = $this->mapRequestedRoute($this->request->requestUri);
        $routeHandler = $requestMethodDictionary[$formattedRoute];

        if (is_null($routeHandler)) {
            return $this->defaultRequestHandler();
        }

        echo call_user_func_array($routeHandler, [$this->request]);
    }

    /**
     * Destructor
     */
    function __destruct()
    {
        $this->resolve();
    }
}
