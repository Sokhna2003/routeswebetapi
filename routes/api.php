<?php
declare(strict_types=1);

use App\Controllers\BookApiController;

$router->get('/api/books', [BookApiController::class, 'index']);
$router->post('/api/books', [BookApiController::class, 'store']);

$router->post('/api/books/{id}/toggle', [BookApiController::class, 'toggle']);   // ← TODO 1b
$router->delete('/api/books/{id}', [BookApiController::class, 'destroy']); 