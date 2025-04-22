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

    public function dispatch($httpMethod, $requestUri) {
        $path = parse_url($requestUri, PHP_URL_PATH);

        // 1) Спроба прямого збігу
        if (isset($this->routes[$httpMethod][$path])) {
            [$ctr, $mtd] = explode('@', $this->routes[$httpMethod][$path]);
            return $this->run($ctr, $mtd, []);
        }

        // 2) Шукаємо шлях з параметрами
        foreach ($this->routes[$httpMethod] as $routePattern => $action) {
            // Перетворюємо /channels/{id} → #^/channels/([^/]+)$#
            $regex = preg_replace('#\{[^/]+\}#', '([^/]+)', $routePattern);
            $regex = '#^' . $regex . '$#';

            if (preg_match($regex, $path, $matches)) {
                array_shift($matches); // перший елемент — повний рядок
                [$ctr, $mtd] = explode('@', $action);
                return $this->run($ctr, $mtd, $matches);
            }
        }

        // 3) Якщо нічого не спрацювало — 404
        http_response_code(404);
        echo "404 Not Found";
    }

    private function run($controller, $method, array $params) {
        require_once __DIR__ . "/Controllers/$controller.php";
        $obj = new $controller;
        call_user_func_array([$obj, $method], $params);
    }
}
