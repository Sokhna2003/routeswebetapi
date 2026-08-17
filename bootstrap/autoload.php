<?php
declare(strict_types=1);

spl_autoload_register(function (string $classe): void {
    $prefixes = [
        'App\\'  => __DIR__ . '/../app/',
        'Core\\' => __DIR__ . '/../core/',
    ];

    foreach ($prefixes as $prefixe => $base) {
        if (str_starts_with($classe, $prefixe)) {
            $chemin = $base . str_replace('\\', '/', substr($classe, strlen($prefixe))) . '.php';
            if (file_exists($chemin)) {
                require $chemin;
                return;
            }
        }
    }
});

if (!defined('VIEW_PATH')) {
    define('VIEW_PATH', __DIR__ . '/../views');
}

if (!defined('DATA_PATH')) {
    define('DATA_PATH', __DIR__ . '/../data');
}
