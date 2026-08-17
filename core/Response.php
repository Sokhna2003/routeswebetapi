<?php
declare(strict_types=1);

namespace Core;

class Response
{
    public function __construct(
        private readonly int $status,
        private readonly array $payload = [],
    ) {}

    public static function json(mixed $data, int $status = 200): self
    {
        return new self($status, ['data' => $data]);  // succès -> { "data": [...] }
    }

    public static function error(string $message, int $status = 400): self
    {
        return new self($status, ['error' => $message, 'code' => $status]); // erreur -> { "error": "...", "code": 404 }
    }

    public function send(): void
    {
        http_response_code($this->status);
        header('Content-Type: application/json; charset=utf-8');  // dit au navigateur "c'est du JSON"
        echo json_encode($this->payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
