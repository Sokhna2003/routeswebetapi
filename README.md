# Atelier 22 — Routes web + routes API (PHP + JS)

Mini-application catalogue de livres illustrant la séparation entre **routes web** (HTML servi par PHP) et **routes API** (JSON consommé par JavaScript avec `fetch`), pour des mises à jour du DOM sans rechargement de page.

## Sommaire

- [Aperçu](#aperçu)
- [Architecture](#architecture)
- [Structure du projet](#structure-du-projet)
- [Installation](#installation)
- [Routes disponibles](#routes-disponibles)
- [Format des réponses API](#format-des-réponses-api)
- [Workflow Git](#workflow-git)

## Aperçu

```
Navigateur
   │
   │  GET /books          ← route WEB → HTML (PageController)
   ▼
Page HTML + books.js
   │
   │  GET  /api/books            ← route API → JSON
   │  POST /api/books            ← route API → JSON
   │  POST /api/books/{id}/toggle ← route API → JSON
   │  DELETE /api/books/{id}     ← route API → JSON
   ▼
Mise à jour du DOM (liste, messages…) sans rechargement de page
```

PHP sert une fois la coquille HTML, puis JavaScript prend le relais et communique avec l'API en JSON pour toutes les interactions (chargement, ajout, disponibilité, suppression).

## Architecture

| Couche | Rôle | Fichiers |
|---|---|---|
| Front-controller | Point d'entrée unique | `public/index.php`, `public/router.php` |
| Router | Route web (HTML) et route API (JSON), gère les paramètres `{id}` | `core/Router.php`, `routes/web.php`, `routes/api.php` |
| Container | Injection de dépendances par réflexion PHP | `core/Container.php` |
| Contrôleur web | Renvoie une `string` HTML | `app/Controllers/PageController.php` |
| Contrôleur API | Renvoie un objet `Response` (JSON) | `app/Controllers/BookApiController.php` |
| Response | Formate et envoie une réponse JSON (succès / erreur) | `core/Response.php` |
| Repository | Lecture/écriture des données dans un fichier JSON | `app/Repositories/BookRepository.php`, `data/books.json` |
| View | Rendu HTML | `core/View.php`, `views/` |
| Frontend | Appels `fetch` + mise à jour du DOM | `public/js/books.js` |

**Le mécanisme clé** : le même `Router` gère les deux types de routes. Il regarde le type de retour du contrôleur pour décider comment répondre :

```php
$result = $controller->{$action}(...$params);

if ($result instanceof Response) {
    $result->send();   // route API -> JSON
    return;
}

echo (string) $result; // route web -> HTML
```

## Structure du projet

```
exercice/
├── app/
│   ├── Controllers/
│   │   ├── PageController.php       (home, books)
│   │   └── BookApiController.php    (index, store, toggle, destroy)
│   └── Repositories/
│       └── BookRepository.php        (all, find, create, toggleDisponible, delete)
├── bootstrap/
│   └── autoload.php                   (autoloader PSR-4 maison)
├── core/
│   ├── Container.php                   (injection de dépendances)
│   ├── Response.php                     (réponses JSON)
│   ├── Router.php                        (routage web + API, paramètres {id})
│   └── View.php                           (rendu des templates)
├── data/
│   └── books.json                          (stockage des livres)
├── public/
│   ├── index.php                            (front-controller)
│   ├── router.php                            (routeur pour php -S)
│   └── js/
│       └── books.js                           (appels fetch, mise à jour du DOM)
├── routes/
│   ├── web.php                                 (routes HTML)
│   └── api.php                                  (routes JSON)
└── views/
    ├── layout.php
    ├── home.php
    └── books.php
```

## Installation

**Prérequis** : PHP 8+, aucune base de données nécessaire (stockage dans `data/books.json`).

1. Cloner le repo :
   ```bash
   git clone https://github.com/sokhna2003/atelier-22-routes-web-api.git
   cd atelier-22-routes-web-api
   ```

2. Lancer le serveur intégré de PHP depuis `public/` :
   ```bash
   cd public
   php -S localhost:8081 router.php
   ```

3. Ouvrir `http://localhost:8081/` dans le navigateur.

Sous Apache (XAMPP), pointer le document root vers `public/` (le `.htaccess` redirige vers `index.php`).

## Routes disponibles

**Routes web** (`routes/web.php`) — renvoient du HTML :

| Méthode | URL | Contrôleur |
|---|---|---|
| GET | `/` | `PageController@home` |
| GET | `/books` | `PageController@books` |

**Routes API** (`routes/api.php`) — renvoient du JSON :

| Méthode | URL | Contrôleur | Description |
|---|---|---|---|
| GET | `/api/books` | `BookApiController@index` | Liste tous les livres |
| POST | `/api/books` | `BookApiController@store` | Crée un livre |
| POST | `/api/books/{id}/toggle` | `BookApiController@toggle` | Bascule disponible/emprunté |
| DELETE | `/api/books/{id}` | `BookApiController@destroy` | Supprime un livre |

## Format des réponses API

**Succès :**
```json
{ "data": [ { "id": 1, "titre": "1984", "auteur": "Orwell", "disponible": true } ] }
```

**Erreur :**
```json
{ "error": "Livre introuvable", "code": 404 }
```

## Workflow Git

Le projet a été construit branche par branche, une par fonctionnalité :

| Branche | Contenu |
|---|---|
| `feature/squelette-core` | Front-controller, router, container, view, response JSON |
| `feature/route-web-books` | Route `GET /books` (coquille HTML) |
| `feature/api-toggle-destroy` | Endpoints API `toggle` et `destroy` |
| `feature/js-fetch` | Appels `fetch` : chargement, création, toggle des livres |
| `feature/js-suppression` | Appel `fetch` DELETE pour la suppression |

Chaque branche a été fusionnée dans `main` après son commit dédié.