<?php
class Router
{
    private array $routes = [];

    public function get(string $uri, array $handler): void
    {
        $this->routes['GET'][$uri] = $handler;
    }

    public function post(string $uri, array $handler): void
    {
        $this->routes['POST'][$uri] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        $uri = rtrim($uri, '/');
        if ($uri === '') $uri = '/';

        // Exact match
        if (isset($this->routes[$method][$uri])) {
            $this->handle($this->routes[$method][$uri]);
            return;
        }

        // Route with parameter /{param}
        foreach ($this->routes[$method] ?? [] as $routeUri => $handler) {
            $regex = $this->buildRegex($routeUri);
            if (preg_match($regex, $uri, $matches)) {
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) $params[$key] = $value;
                }
                $this->handle($handler, $params);
                return;
            }
        }

        http_response_code(404);
        $this->renderError(404, 'Page not found');
    }

    private function buildRegex(string $routeUri): string
    {
        $pattern = preg_replace('/\/\{([a-zA-Z_]+)\}/', '/(?P<$1>[^/]+)', $routeUri);
        return '#^' . $pattern . '$#';
    }

    private function handle(array $handler, array $params = []): void
    {
        $middleware = $handler['middleware'] ?? [];
        $controllerClass = $handler['controller'];
        $action = $handler['action'];

        foreach ($middleware as $mw) {
            if ($mw === 'guest') {
                if (Session::get('user_id')) { $this->doRedirect('/'); return; }
            } elseif ($mw === 'auth') {
                if (!Session::get('user_id')) { $this->doRedirect('/login'); return; }
            } elseif ($mw === 'admin') {
                if (!Session::get('user_id')) { $this->doRedirect('/login'); return; }
                if (Session::get('role') !== 'admin') {
                    http_response_code(403);
                    $this->renderError(403, 'Access denied');
                    return;
                }
            } elseif ($mw === 'teacher') {
                if (!Session::get('user_id')) { $this->doRedirect('/login'); return; }
                if (Session::get('role') !== 'teacher') {
                    http_response_code(403);
                    $this->renderError(403, 'Access denied');
                    return;
                }
            } elseif ($mw === 'student') {
                if (!Session::get('user_id')) { $this->doRedirect('/login'); return; }
                if (Session::get('role') !== 'student') {
                    http_response_code(403);
                    $this->renderError(403, 'Access denied');
                    return;
                }
            }
        }

        // CSRF check for POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!BaseController::verifyCsrf()) {
                $this->renderError(403, 'Invalid or missing CSRF token');
                return;
            }
        }

        $controller = new $controllerClass();
        if (!method_exists($controller, $action)) {
            $this->renderError(500, 'Controller action not found');
            return;
        }
        $controller->$action(...$params);
    }

    private function doRedirect(string $uri): void
    {
        header('Location: ' . APP_URL . $uri);
        exit;
    }

    private function renderError(int $code, string $message): void
    {
        $title = match($code) {
            403 => '403 Forbidden',
            404 => '404 Not Found',
            default => 'Server Error',
        };
        $viewPath = BASE_PATH . "/views/errors/{$code}.php";
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "<!DOCTYPE html><html><head><title>{$title}</title></head><body style='text-align:center;padding:50px'>
                <h1>{$code}</h1><p>{$message}</p>
                <a href='" . APP_URL . "/'>Go Home</a></body></html>";
        }
    }
}
