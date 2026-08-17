<?php
declare(strict_types=1);

use App\Repositories\BookRepository;
use Core\Container;
use Core\Router;

require __DIR__ . '/../bootstrap/autoload.php';

$container = new Container();
$container->bind(
    BookRepository::class,
    static fn (): BookRepository => new BookRepository(DATA_PATH . '/books.json')
);

$router = new Router();
require __DIR__ . '/../routes/web.php';  // charge les routes HTML
require __DIR__ . '/../routes/api.php';  // charge les routes JSON

$base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if ($base !== '/' && str_starts_with($path, $base)) {
    $path = substr($path, strlen($base));
}
if ($path === '') {
    $path = '/';
}

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $path, $container);
