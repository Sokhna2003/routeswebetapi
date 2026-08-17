<?php
declare(strict_types=1);

namespace App\Repositories;

class BookRepository
{
    public function __construct(private string $fichier) {}

    /** @return list<array{id:int,titre:string,auteur:string,disponible:bool}> */
    public function all(): array
    {
        return $this->lire();
    }

    /** @return array{id:int,titre:string,auteur:string,disponible:bool}|null */
    public function find(int $id): ?array
    {
        foreach ($this->lire() as $livre) {
            if ((int) $livre['id'] === $id) {
                return $livre;
            }
        }
        return null;
    }

    /**
     * @param array{titre:string,auteur:string,disponible?:bool} $data
     * @return array{id:int,titre:string,auteur:string,disponible:bool}
     */
    public function create(array $data): array
    {
        $livres = $this->lire();
        $nextId = $livres === [] ? 1 : max(array_column($livres, 'id')) + 1;

        $livre = [
            'id'         => $nextId,
            'titre'      => trim($data['titre']),
            'auteur'     => trim($data['auteur']),
            'disponible' => (bool) ($data['disponible'] ?? true),
        ];

        $livres[] = $livre;
        $this->ecrire($livres);

        return $livre;
    }

    /** @return array{id:int,titre:string,auteur:string,disponible:bool}|null */
    public function toggleDisponible(int $id): ?array
    {
        $livres = $this->lire();
        foreach ($livres as $i => $livre) {
            if ((int) $livre['id'] === $id) {
                $livres[$i]['disponible'] = !((bool) $livre['disponible']);
                $this->ecrire($livres);
                return $livres[$i];
            }
        }
        return null;
    }

    public function delete(int $id): bool
    {
        $livres = $this->lire();
        $avant = count($livres);
        $livres = array_values(array_filter(
            $livres,
            static fn (array $l): bool => (int) $l['id'] !== $id
        ));

        if (count($livres) === $avant) {
            return false;
        }

        $this->ecrire($livres);
        return true;
    }

    /** @return list<array{id:int,titre:string,auteur:string,disponible:bool}> */
    private function lire(): array
    {
        if (!is_file($this->fichier)) {
            return [];
        }

        $raw = file_get_contents($this->fichier);
        $data = json_decode((string) $raw, true);

        return is_array($data) ? $data : [];
    }

    /** @param list<array{id:int,titre:string,auteur:string,disponible:bool}> $livres */
    private function ecrire(array $livres): void
    {
        file_put_contents(
            $this->fichier,
            json_encode($livres, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }
}
