<?php
/**
 * Created by PhpStorm.
 * User: Gabriel B.R
 * Email: gabriel.bentara@gmail.com
 * Date: 13/10/18
 * Time: 12:30 PM
 */

require "core/Request.php";

class Router
{
    private $getRoutes = [];
    private $postRoutes = [];
    private $putRoutes = [];
    private $deleteRoutes = [];
    private $middlewares = [];

    public function __call($method, $args)
    {
        $routes = strtolower($method) . "Routes";
        $url = $args[0];
        $executedMethod = $args[1];
        $newRoute = [$url => $executedMethod];
        $this->$routes = array_merge($this->$routes, $newRoute);
    }

    public function check($middleware, $method)
    {
        if (array_key_exists($method, $this->middlewares)) {
            array_push($this->middlewares[$method], $middleware);
            return;
        }

        $this->middlewares[$method] = [$middleware];
    }

    public function execute()
    {
        $result = $this->findRoute();
        $method = $result[0];
        $vars = $result[1];

        if ($method == "") {
            echo "404 not found";
            return;
        }

        $request = new Request($vars);

        $executed = $this->executeMiddleware($method, $request);
        if ($executed) {
            return;
        }

        $parsedMethod = explode("@", $method);
        $controller = $this->getControllerInstance($parsedMethod[0], $request);

        if ($controller != null) {
            $methodName = $parsedMethod[1];
            $controller->$methodName();
        }
    }

    public function executeMiddleware($method, $request)
    {
        if (!isset($this->middlewares[$method])) {
            return False;
        }

        $middlewares = $this->middlewares[$method];
        foreach ($middlewares as $m) {
            $middleware = "middleware/" . $m . ".php";
            require_once $middleware;
            $result = $m::execute($request);
            if ($result) {
                return $result;
            }
        }

        return False;
    }

    public function getControllerInstance($class, $request)
    {
        $file = "controller/" . $class . "Controller.php";
        if (!is_file($file)) {
            echo "Controller not found";
            return null;
        }

        include $file;
        $class = $class . "Controller";
        return new $class($request);
    }

    public function findRoute()
    {
        $requestURI = $_SERVER['REQUEST_URI'];
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $routes = strtolower($requestMethod) . "Routes";
        $vars = [];
        $routeMethod = "";

        foreach ($this->$routes as $url => $method) {
            $urlPattern = $this->buildRegex($url);
            $result = preg_match($urlPattern, $requestURI, $values);
            preg_match_all("/:([a-z]+)/", $url, $names);

            if (!$result) {
                continue;
            }

            for ($i = 0; $i < count($names[1]); $i++) {
                $vars[$names[1][$i]] = $values[$i+1];
            }

            $routeMethod = $method;

            break;
        }

        return [$routeMethod, $vars];
    }

    public function buildRegex($route)
    {
        $regex = preg_replace("/(:[a-z]+)/", "([a-zA-Z0-9\-\._\@%]+)", $route);
        $regex = "/" . preg_replace("/\//", "\/", $regex) . "($|\/|\?)/";
        return $regex;
    }
}