# Ynov Basket - Projet Compensatoire B2

## 🏀 Description

Application web Symfony pour consulter les statistiques NBA via l'API BallDontLie.io. Cette application permet aux utilisateurs de consulter les données des joueurs, équipes et matchs de la NBA.

## ✨ Fonctionnalités

### 🔐 Authentification
- **Inscription** : Création de compte avec email et mot de passe
- **Connexion** : Authentification sécurisée
- **Déconnexion** : Session sécurisée

### 👥 Joueurs
- **Liste des joueurs** : Affichage de tous les joueurs NBA avec pagination
- **Détails d'un joueur** : Informations complètes (nom, prénom, taille, poids, équipe, position)
- **Navigation** : Clic sur une carte pour voir les détails du joueur
- **Lien vers l'équipe** : Accès direct à l'équipe du joueur

### 🏆 Équipes
- **Liste des équipes** : Affichage de toutes les équipes NBA
- **Détails d'une équipe** : Informations complètes (nom, ville, conférence, division)
- **Joueurs de l'équipe** : Liste des joueurs membres de l'équipe
- **Navigation** : Clic sur une carte pour voir les détails de l'équipe

### 🎮 Matchs
- **Liste des matchs** : Affichage de tous les matchs NBA avec pagination
- **Détails d'un match** : Informations complètes (date, score, statut, équipes)
- **Équipes participantes** : Accès direct aux équipes qui s'affrontent
- **Navigation** : Clic sur une carte pour voir les détails du match

## 🛠️ Technologies utilisées

- **Backend** : Symfony 6.4
- **Frontend** : Twig, Bootstrap 5, Font Awesome
- **Base de données** : SQLite
- **API** : BallDontLie.io
- **Authentification** : Symfony Security Bundle

## 🚀 Installation et configuration

### Prérequis
- PHP 8.1 ou supérieur
- Composer
- Symfony CLI (optionnel)

### Installation

1. **Cloner le projet**
```bash
git clone <repository-url>
cd Project-Compensatoire
```

2. **Installer les dépendances**
```bash
composer install
```

3. **Configurer la base de données**
```bash
# La base de données SQLite sera créée automatiquement
php bin/console doctrine:migrations:migrate
```

4. **Démarrer le serveur de développement**
```bash
# Option 1 : Avec Symfony CLI
symfony server:start

# Option 2 : Avec PHP intégré
php -S localhost:8000 -t public
```

5. **Accéder à l'application**
```
http://localhost:8000
```

## 📁 Structure du projet

```
Project-Compensatoire/
├── src/
│   ├── Controller/           # Contrôleurs
│   │   ├── MainController.php
│   │   ├── SecurityController.php
│   │   ├── PlayerController.php
│   │   ├── TeamController.php
│   │   └── GameController.php
│   ├── Entity/              # Entités Doctrine
│   │   └── User.php
│   ├── Form/                # Formulaires
│   │   └── RegistrationFormType.php
│   ├── Repository/          # Repositories
│   ├── Security/            # Sécurité
│   │   └── AuthNBAAuthenticator.php
│   └── Service/             # Services
│       └── NbaApiService.php
├── templates/               # Templates Twig
│   ├── base.html.twig
│   ├── security/
│   │   ├── login.html.twig
│   │   └── register.html.twig
│   ├── main/
│   │   ├── players.html.twig
│   │   ├── teams.html.twig
│   │   └── games.html.twig
│   ├── player/
│   │   └── show.html.twig
│   ├── team/
│   │   └── show.html.twig
│   └── game/
│       └── show.html.twig
├── config/                  # Configuration
├── migrations/              # Migrations Doctrine
├── public/                  # Fichiers publics
└── var/                     # Cache et logs
```

## 🎨 Design et UX

### Interface utilisateur
- **Design moderne** : Interface responsive avec Bootstrap 5
- **Thème NBA** : Couleurs et icônes inspirées du basket-ball
- **Navigation intuitive** : Menu de navigation clair et accessible
- **Cartes interactives** : Design en cartes pour une meilleure expérience utilisateur

### Fonctionnalités UX
- **Pagination** : Navigation facile entre les pages
- **Recherche visuelle** : Cartes cliquables pour accéder aux détails
- **Breadcrumbs** : Navigation contextuelle
- **Messages flash** : Notifications utilisateur
- **Responsive** : Compatible mobile et desktop

## 🔧 Configuration

### Variables d'environnement
Le fichier `.env` contient les configurations suivantes :
- `DATABASE_URL` : URL de la base de données (SQLite par défaut)
- `APP_SECRET` : Clé secrète de l'application

### API Configuration
L'API BallDontLie.io est configurée dans `src/Service/NbaApiService.php` :
- URL de base : `https://www.balldontlie.io/api/v1`
- Clé API : `4b53f95b-7ce3-4ff3-bd76-b7b6474e6b3b`

## 🧪 Tests

Pour exécuter les tests (si configurés) :
```bash
php bin/phpunit
```

## 📝 Notes de développement

### Architecture
- **MVC** : Architecture Model-View-Controller
- **Services** : Services pour l'interaction avec l'API
- **Sécurité** : Authentification personnalisée avec Symfony Security
- **Templates** : Templates Twig avec héritage

### Bonnes pratiques
- **Séparation des responsabilités** : Contrôleurs, services et templates séparés
- **Gestion d'erreurs** : Try-catch pour les appels API
- **Validation** : Validation des formulaires
- **Sécurité** : Authentification et autorisation

## 🚀 Déploiement

### Production
1. Configurer les variables d'environnement
2. Optimiser le cache : `php bin/console cache:clear --env=prod`
3. Configurer le serveur web (Apache/Nginx)
4. Déployer les fichiers

### Développement
1. Cloner le projet
2. Installer les dépendances
3. Configurer la base de données
4. Démarrer le serveur de développement

## 📄 Licence

Ce projet est développé dans le cadre du projet compensatoire B2 d'Ynov.

## 👥 Auteur

Développé pour le projet compensatoire Ynov Basket.

