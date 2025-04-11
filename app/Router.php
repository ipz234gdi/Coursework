<?php
class Router {
    private $routes = [];

    public function get($uri, $action) {
        $this->addRoute('GET', $uri, $action);
    }

    public function post($uri, $action) {
        $this->addRoute('POST', $uri, $action);
    }

    private function addRoute($method, $uri, $action) {
        $this->routes[$method][$uri] = $action;
    }

    public function dispatch($method, $uri) {
        $uri = parse_url($uri, PHP_URL_PATH);
        $action = $this->routes[$method][$uri] ?? null;

        if (!$action) {
            http_response_code(404);
            echo "404 Not Found";
            return;
        }

        list($controller, $methodName) = explode('@', $action);
        require_once __DIR__ . "/Controllers/$controller.php";

        $controllerObj = new $controller;
        $controllerObj->$methodName();
    }
}
