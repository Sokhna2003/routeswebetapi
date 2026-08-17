<?php
declare(strict_types=1);

use App\Controllers\PageController;

$router->get('/', [PageController::class, 'home']);
$router->get('/books', [PageController::class, 'books']);