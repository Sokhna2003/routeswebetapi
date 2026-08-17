<?php
declare(strict_types=1);

namespace Core;

class View
{
    public static function render(string $template, array $data = []): string
    {
        $data['base'] = self::baseUrl();
        extract($data, EXTR_SKIP);
        ob_start();
        include VIEW_PATH . '/' . $template . '.php';
        return (string) ob_get_clean();
    }

    public static function baseUrl(): string
    {
        $base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
        return $base === '/' ? '' : rtrim($base, '/');
    }
}
