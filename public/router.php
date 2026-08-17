<?php
declare(strict_types=1);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file = __DIR__ . $path;

if ($path !== '/' && is_file($file)) {
    return false;  // fichier statique (ex: /js/books.js) -> le serveur PHP le sert directement
}

require __DIR__ . '/index.php';   // sinon on passe par le front-controller
