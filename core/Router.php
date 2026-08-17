<?php
declare(strict_types=1);

namespace Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array $action): void
    {
        $this->add('GET', $path, $action);
    }

    public function post(string $path, array $action): void
    {
        $this->add('POST', $path, $action);
    }

    public function delete(string $path, array $action): void
    {
        $this->add('DELETE', $path, $action);
    }

    private function add(string $method, string $path, array $action): void
    {
        $this->routes[] = [
            'method' => $method,
            'path'   => $path,
            'action' => $action,
        ];
    }

    public function dispatch(string $method, string $path, Container $container): void
    {
        if ($method === 'POST' && strtoupper($_POST['_method'] ?? '') === 'DELETE') {
            $method = 'DELETE';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->match($route['path'], $path);
            if ($params === null) {
                continue;
            }

            [$class, $action] = $route['action'];
            $controller = $container->make($class);
            $result = $controller->{$action}(...$params);

            if ($result instanceof Response) {
                $result->send();  // route API -> en-tête JSON + corps JSON
                return;
            }

            echo (string) $result;  // route web -> simple echo du HTML
            return;
        }

        http_response_code(404);
        if (str_starts_with($path, '/api')) {
            Response::error('Endpoint introuvable', 404)->send();
            return;
        }

        echo '404 - Page introuvable';
    }

    // Il gère les routes avec paramètres comme /api/books/{id}, via une regex 
    /** @return list<int|string>|null */
    private function match(string $pattern, string $path): ?array
    {
        $regex = preg_replace('#\{[a-zA-Z_]+\}#', '([^/]+)', $pattern);
        // "/api/books/{id}" devient "/api/books/([^/]+)"
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $path, $matches)) {
            return null;
        }

        array_shift($matches); // enlève le match complet, garde juste les groupes capturés
        return $matches; // ex: ['5'] si /api/books/5
    }
}
