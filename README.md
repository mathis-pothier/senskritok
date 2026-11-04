# <h1 style = "background-color:gray; border-radius:10px; text-align:center; padding:0px"> SENSKRITOK </h1>

API développée avec Symfony pour ajouter des films, leur ajouter une courte description ainsi que leur année de sortie. Elle permet également d'associer une ou plusieurs catégories.

Possibilité de créer, consulter et filtrer.

---

## Prérequis

Avant de commencer, vérifie que tu as installé :

| Outil | Version | Commande bash pour vérifier |
|---|---|---|
| [PHP](https://www.php.net/downloads) | 8.2 ou + | `php -v` |
| [Composer](https://getcomposer.org/download/) | 2.x | `composer -V` |
| [Git](https://git-scm.com/downloads) | — | `git --version` |
| [Symfony CLI](https://symfony.com/download) | — | `symfony -V` |

---

## Installation

### 1. Cloner le projet

```bash
git clone https://github.com/mathis-pothier/senskritok.git
cd senskritok
```

### 2. Installer les dépendances

Créer le dossier `vendor` qui n'est pas versionné

```bash
composer install
```

### 3. Créer la base de données native

```bash
php bin/console doctrine:database:create
```
**/!\ Utilise la base de donnée SQLite et non mySQL.**

### 4. Créer les tables

```bash
php bin/console doctrine:migrations:migrate
```

### 6. Lancer le serveur

```bash
symfony serve
```

ou, sans la Symfony CLI :

```bash
php -S localhost:8000 -t public
```

L'API est disponible sur **http://localhost:8000**.

---

## Routes de l'API

Pour voir toutes les routes disponibles :

```bash
php bin/console debug:router
```

### Films

| Méthode | URL | Description |
|---|---|---|
| `GET` | `/movies` | Liste de tous les films |
| `GET` | `/movies?page=1&limit=5` | Page 1, avec 5 films par page |
| `GET` | `/movies?title=aven` | Filtre les films dont le titre contient « aven » |
| `GET` | `/movies/{id}` | Détail d'un film via son ID |
| `POST` | `/movies` | Ajouter un film |
| `POST`| `/categories` | Ajouter une catégorie |
| `PUT` | `/movies/{id}` | Modifier un film via son ID |
| `DELETE` | `/movies/{id}` | Supprimer un film via son ID |

**Exemple de création :**

```http
POST http://localhost:8000/movies
Content-Type: application/json

{
    "title": "Conjuring",
    "description": "A couple discover a supernatural presence in their home.",
    "year": 2013,
    "categories": [1, 2]
}
```

**Exemple de réponse :**

```json
{
    "id": 1,
    "title": "Conjuring",
    "description": "...",
    "year": 2013,
    "categories": [
        { "id": 1, "name": "Horreur" }
    ]
}

```

### Catégories

```http
POST http://localhost:8000/categories
Content-Type: application/json

{
    "name": "Horreur"
}
```

> Des exemples de requêtes sont disponibles dans le fichier `api.http` du projet.
> Avec VS Code, installer l'extension **httpYac** pour les lancer
> directement depuis l'éditeur.

## Extensions nécessaires 

* PHP Intelephense
* PHP Namespace Resolver
* HttpYac
* Live Preview
* SQLite3 Editor

---

## Commandes utiles

| Commande | Rôle |
|---|---|
| `php bin/console cache:clear` | Vide le cache |
| `php bin/console debug:router` | Liste toutes les routes |
| `php bin/console router:match /movies --method=GET` | Indique quelle route répond à une URL |
| `php bin/console make:migration` | Génère une migration après une modification d'entité |

---

## Technologies

- [Symfony](https://symfony.com/)
- [Doctrine ORM](https://www.doctrine-project.org/)